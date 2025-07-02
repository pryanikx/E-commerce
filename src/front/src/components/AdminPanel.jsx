import React, { useState, useEffect, useCallback } from 'react';
import api from '../api/api';
import ResourceTable from './ResourceTable';
import ProductForm from './ProductForm';
import CategoryForm from './CategoryForm';
import ManufacturerForm from './ManufacturerForm';
import MaintenanceForm from './MaintenanceForm';

const AdminPanel = () => {
    const [products, setProducts] = useState([]);
    const [categories, setCategories] = useState([]);
    const [manufacturers, setManufacturers] = useState([]);
    const [maintenances, setMaintenances] = useState([]);
    const [editingProduct, setEditingProduct] = useState(null);
    const [editingCategory, setEditingCategory] = useState(null);
    const [editingManufacturer, setEditingManufacturer] = useState(null);
    const [editingMaintenance, setEditingMaintenance] = useState(null);
    const [pagination, setPagination] = useState({
        current_page: 1,
        per_page: 20,
        total: 0,
        last_page: 1,
    });
    const [isLoading, setIsLoading] = useState(false);
    const [isExporting, setIsExporting] = useState(false);
    const [exportStatus, setExportStatus] = useState('');
    const [loadingStates, setLoadingStates] = useState({
        categories: false,
        manufacturers: false,
        maintenances: false
    });
    const [errors, setErrors] = useState({
        categories: '',
        manufacturers: '',
        maintenances: ''
    });

    // Memoized function to fetch products
    const fetchProducts = useCallback(async (page = 1) => {
        setIsLoading(true);
        try {
            console.log('Fetching products for page:', page);
            const response = await api.get('/admin/products', {
                params: { page },
                headers: {
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            });

            console.log('Products API Response:', response.data);

            if (response.data && response.data.data) {
                setProducts(response.data.data);

                if (response.data.meta) {
                    setPagination(response.data.meta);
                    console.log('Updated pagination:', response.data.meta);
                }
            } else {
                console.error('Unexpected API response structure:', response.data);
            }
        } catch (error) {
            console.error('Failed to fetch products:', error);
        } finally {
            setIsLoading(false);
        }
    }, []);

    const fetchCategories = useCallback(async () => {
        setLoadingStates(prev => ({ ...prev, categories: true }));
        setErrors(prev => ({ ...prev, categories: '' }));

        try {
            console.log('Fetching categories...');
            const response = await api.get('/admin/categories', {
                headers: {
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            });

            console.log('Categories API Response:', response.data);

            if (response.data) {
                if (Array.isArray(response.data)) {
                    setCategories(response.data);
                } else if (response.data.data && Array.isArray(response.data.data)) {
                    setCategories(response.data.data);
                } else if (response.data.message && response.data.message.includes('empty')) {
                    setCategories([]);
                } else {
                    console.warn('Unexpected categories response structure:', response.data);
                    setCategories([]);
                }
            }
        } catch (error) {
            console.error('Failed to fetch categories:', error);
            setErrors(prev => ({
                ...prev,
                categories: `Failed to load categories: ${error.response?.data?.message || error.message}`
            }));
            setCategories([]);
        } finally {
            setLoadingStates(prev => ({ ...prev, categories: false }));
        }
    }, []);

    const fetchManufacturers = useCallback(async () => {
        setLoadingStates(prev => ({ ...prev, manufacturers: true }));
        setErrors(prev => ({ ...prev, manufacturers: '' }));

        try {
            console.log('Fetching manufacturers...');
            const response = await api.get('/admin/manufacturers', {
                headers: {
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            });

            console.log('Manufacturers API Response:', response.data);

            if (response.data) {
                if (Array.isArray(response.data)) {
                    setManufacturers(response.data);
                } else if (response.data.data && Array.isArray(response.data.data)) {
                    setManufacturers(response.data.data);
                } else if (response.data.message && response.data.message.includes('empty')) {
                    setManufacturers([]);
                } else {
                    console.warn('Unexpected manufacturers response structure:', response.data);
                    setManufacturers([]);
                }
            }
        } catch (error) {
            console.error('Failed to fetch manufacturers:', error);
            setErrors(prev => ({
                ...prev,
                manufacturers: `Failed to load manufacturers: ${error.response?.data?.message || error.message}`
            }));
            setManufacturers([]);
        } finally {
            setLoadingStates(prev => ({ ...prev, manufacturers: false }));
        }
    }, []);

    const fetchMaintenances = useCallback(async () => {
        setLoadingStates(prev => ({ ...prev, maintenances: true }));
        setErrors(prev => ({ ...prev, maintenances: '' }));

        try {
            console.log('Fetching maintenances...');
            const response = await api.get('/admin/maintenance', {
                headers: {
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            });

            console.log('Maintenances API Response:', response.data);

            if (response.data) {
                if (Array.isArray(response.data)) {
                    setMaintenances(response.data);
                } else if (response.data.data && Array.isArray(response.data.data)) {
                    setMaintenances(response.data.data);
                } else if (response.data.message && response.data.message.includes('empty')) {
                    setMaintenances([]);
                } else {
                    console.warn('Unexpected maintenances response structure:', response.data);
                    setMaintenances([]);
                }
            }
        } catch (error) {
            console.error('Failed to fetch maintenances:', error);
            setErrors(prev => ({
                ...prev,
                maintenances: `Failed to load maintenances: ${error.response?.data?.message || error.message}`
            }));
            setMaintenances([]);
        } finally {
            setLoadingStates(prev => ({ ...prev, maintenances: false }));
        }
    }, []);

    useEffect(() => {
        fetchProducts(pagination.current_page);
        fetchCategories();
        fetchManufacturers();
        fetchMaintenances();
    }, []);

    useEffect(() => {
        fetchProducts(pagination.current_page);
    }, [pagination.current_page, fetchProducts]);

    const handleExportCatalog = async () => {
        setIsExporting(true);
        setExportStatus('');

        try {
            const response = await api.post('/admin/export/catalog');

            if (response.data.export_id) {
                setExportStatus(`Экспорт запущен успешно! ID: ${response.data.export_id}. Проверьте email для получения уведомления.`);
            } else {
                setExportStatus('Экспорт запущен успешно! Проверьте email для получения уведомления.');
            }
        } catch (error) {
            console.error('Failed to export catalog:', error);
            setExportStatus(`Ошибка при экспорте: ${error.response?.data?.message || error.message}`);
        } finally {
            setIsExporting(false);

            setTimeout(() => setExportStatus(''), 10000);
        }
    };

    const handleCreateProduct = async (data, headers) => {
        if (!data) {
            setEditingProduct(null);
            return;
        }
        try {
            await api.post('/admin/products', data, { headers });
            await fetchProducts(pagination.current_page);
            setEditingProduct(null);
        } catch (error) {
            console.error('Failed to create product:', {
                message: error.message,
                response: error.response?.data,
                status: error.response?.status,
            });
            throw error;
        }
    };

    const handleUpdateProduct = async (data, headers) => {
        if (!data) {
            setEditingProduct(null);
            return;
        }
        try {
            if (data instanceof FormData) {
                console.log('Updating product with FormData via PUT - converting to POST with _method');
                await api.post(`/admin/products/${editingProduct.id}?_method=PUT`, data, {
                    headers: {
                        ...headers,
                        'X-HTTP-Method-Override': 'PUT'
                    }
                });
            } else {
                console.log('Updating product with JSON via PUT');
                await api.put(`/admin/products/${editingProduct.id}`, data, { headers });
            }
            await fetchProducts(pagination.current_page);
            setEditingProduct(null);
        } catch (error) {
            console.error('Failed to update product:', {
                message: error.message,
                response: error.response?.data,
                status: error.response?.status,
            });
            throw error;
        }
    };

    const handleDeleteProduct = async (id) => {
        try {
            await api.delete(`/admin/products/${id}`);
            await fetchProducts(pagination.current_page);
        } catch (error) {
            console.error('Failed to delete product:', error);
        }
    };

    const handleCreateCategory = async (data) => {
        if (!data) {
            setEditingCategory(null);
            return;
        }
        try {
            await api.post('/admin/categories', data);
            await fetchCategories();
            setEditingCategory(null);
        } catch (error) {
            console.error('Failed to create category:', error);
            throw error;
        }
    };

    const handleUpdateCategory = async (data) => {
        if (!data) {
            setEditingCategory(null);
            return;
        }
        try {
            await api.put(`/admin/categories/${editingCategory.id}`, data);
            await fetchCategories();
            setEditingCategory(null);
        } catch (error) {
            console.error('Failed to update category:', error);
            throw error;
        }
    };

    const handleDeleteCategory = async (id) => {
        try {
            await api.delete(`/admin/categories/${id}`);
            setCategories(categories.filter((c) => c.id !== id));
        } catch (error) {
            console.error('Failed to delete category:', error);
        }
    };

    const handleCreateManufacturer = async (data) => {
        if (!data) {
            setEditingManufacturer(null);
            return;
        }
        try {
            await api.post('/admin/manufacturers', data);
            await fetchManufacturers();
            setEditingManufacturer(null);
        } catch (error) {
            console.error('Failed to create manufacturer:', error);
            throw error;
        }
    };

    const handleUpdateManufacturer = async (data) => {
        if (!data) {
            setEditingManufacturer(null);
            return;
        }
        try {
            await api.put(`/admin/manufacturers/${editingManufacturer.id}`, data);
            await fetchManufacturers();
            setEditingManufacturer(null);
        } catch (error) {
            console.error('Failed to update manufacturer:', error);
            throw error;
        }
    };

    const handleDeleteManufacturer = async (id) => {
        try {
            await api.delete(`/admin/manufacturers/${id}`);
            setManufacturers(manufacturers.filter((m) => m.id !== id));
        } catch (error) {
            console.error('Failed to delete manufacturer:', error);
        }
    };

    const handleCreateMaintenance = async (data) => {
        if (!data) {
            setEditingMaintenance(null);
            return;
        }
        try {
            await api.post('/admin/maintenance', data);
            await fetchMaintenances();
            setEditingMaintenance(null);
        } catch (error) {
            console.error('Failed to create maintenance:', error);
            throw error;
        }
    };

    const handleUpdateMaintenance = async (data) => {
        if (!data) {
            setEditingMaintenance(null);
            return;
        }
        try {
            await api.put(`/admin/maintenance/${editingMaintenance.id}`, data);
            await fetchMaintenances();
            setEditingMaintenance(null);
        } catch (error) {
            console.error('Failed to update maintenance:', error);
            throw error;
        }
    };

    const handleDeleteMaintenance = async (id) => {
        try {
            await api.delete(`/admin/maintenance/${id}`);
            setMaintenances(maintenances.filter((m) => m.id !== id));
        } catch (error) {
            console.error('Error deleting maintenance:', error);
        }
    };

    const handleEditProduct = (item) => {
        setEditingProduct(item);
        setEditingCategory(null);
        setEditingManufacturer(null);
        setEditingMaintenance(null);
    };

    const handleCreateProductClick = () => {
        setEditingProduct({});
        setEditingCategory(null);
        setEditingManufacturer(null);
        setEditingMaintenance(null);
    };

    const handleEditCategory = (item) => {
        setEditingCategory(item);
        setEditingProduct(null);
        setEditingManufacturer(null);
        setEditingMaintenance(null);
    };

    const handleCreateCategoryClick = () => {
        setEditingCategory({});
        setEditingProduct(null);
        setEditingManufacturer(null);
        setEditingMaintenance(null);
    };

    const handleEditManufacturer = (item) => {
        setEditingManufacturer(item);
        setEditingProduct(null);
        setEditingCategory(null);
        setEditingMaintenance(null);
    };

    const handleCreateManufacturerClick = () => {
        setEditingManufacturer({});
        setEditingProduct(null);
        setEditingCategory(null);
        setEditingMaintenance(null);
    };

    const handleEditMaintenance = (item) => {
        setEditingMaintenance(item);
        setEditingProduct(null);
        setEditingCategory(null);
        setEditingManufacturer(null);
    };

    const handleCreateMaintenanceClick = () => {
        setEditingMaintenance({});
        setEditingProduct(null);
        setEditingCategory(null);
        setEditingManufacturer(null);
    };

    const handleCancel = () => {
        setEditingProduct(null);
        setEditingCategory(null);
        setEditingManufacturer(null);
        setEditingMaintenance(null);
    };

    const handlePageChange = (page) => {
        console.log('Changing page from', pagination.current_page, 'to:', page);
        if (page !== pagination.current_page) {
            setPagination((prev) => ({ ...prev, current_page: page }));
        }
    };

    const renderPagination = () => {
        const pages = [];
        const maxPagesToShow = 10;
        let startPage = Math.max(1, pagination.current_page - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(pagination.last_page, startPage + maxPagesToShow - 1);

        if (endPage - startPage + 1 < maxPagesToShow) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }

        if (startPage > 1) {
            pages.push(
                <button
                    key={1}
                    onClick={() => handlePageChange(1)}
                    disabled={isLoading}
                    className="px-4 py-2 mx-1 border rounded bg-white text-blue-500"
                >
                    1
                </button>
            );
            if (startPage > 2) {
                pages.push(<span key="ellipsis-start" className="px-2">...</span>);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            pages.push(
                <button
                    key={i}
                    onClick={() => handlePageChange(i)}
                    disabled={isLoading}
                    className={`px-4 py-2 mx-1 border rounded ${
                        pagination.current_page === i
                            ? 'bg-blue-500 text-white'
                            : 'bg-white text-blue-500'
                    } ${isLoading ? 'opacity-50 cursor-not-allowed' : ''}`}
                >
                    {i}
                </button>
            );
        }

        if (endPage < pagination.last_page) {
            if (endPage < pagination.last_page - 1) {
                pages.push(<span key="ellipsis-end" className="px-2">...</span>);
            }
            pages.push(
                <button
                    key={pagination.last_page}
                    onClick={() => handlePageChange(pagination.last_page)}
                    disabled={isLoading}
                    className="px-4 py-2 mx-1 border rounded bg-white text-blue-500"
                >
                    {pagination.last_page}
                </button>
            );
        }

        return (
            <div className="flex justify-center mt-4 gap-2">
                <button
                    onClick={() => handlePageChange(pagination.current_page - 1)}
                    disabled={pagination.current_page === 1 || isLoading}
                    className={`px-4 py-2 border rounded bg-white text-blue-500 ${
                        pagination.current_page === 1 || isLoading
                            ? 'opacity-50 cursor-not-allowed'
                            : ''
                    }`}
                >
                    Previous
                </button>
                {pages}
                <button
                    onClick={() => handlePageChange(pagination.current_page + 1)}
                    disabled={pagination.current_page === pagination.last_page || isLoading}
                    className={`px-4 py-2 border rounded bg-white text-blue-500 ${
                        pagination.current_page === pagination.last_page || isLoading
                            ? 'opacity-50 cursor-not-allowed'
                            : ''
                    }`}
                >
                    Next
                </button>
            </div>
        );
    };

    return (
        <div className="container mx-auto p-4">
            <h1 className="text-3xl font-bold mb-8">Admin Panel</h1>

            <h2 className="text-2xl font-bold mb-4">Products</h2>

            <div className="mb-4 flex justify-between items-center">
                <div className="flex gap-2">
                    <button
                        onClick={handleCreateProductClick}
                        className="bg-blue-500 text-white p-2 rounded hover:bg-blue-600 transition-colors"
                    >
                        Create Product
                    </button>

                    <button
                        onClick={handleExportCatalog}
                        disabled={isExporting}
                        className={`p-2 rounded transition-colors ${
                            isExporting
                                ? 'bg-gray-400 cursor-not-allowed'
                                : 'bg-green-500 hover:bg-green-600'
                        } text-white`}
                    >
                        {isExporting ? (
                            <>
                                <span className="inline-block animate-spin mr-2">⏳</span>
                                Exporting...
                            </>
                        ) : (
                            <>
                                📤 Export Catalog
                            </>
                        )}
                    </button>
                </div>

                <div className="text-sm text-gray-600">
                    Total Products: {pagination.total} | Page {pagination.current_page} of {pagination.last_page}
                </div>
            </div>

            {exportStatus && (
                <div className={`mb-4 p-3 rounded ${
                    exportStatus.includes('Ошибка')
                        ? 'bg-red-100 border border-red-400 text-red-700'
                        : 'bg-green-100 border border-green-400 text-green-700'
                }`}>
                    {exportStatus}
                </div>
            )}

            {editingProduct !== null && (
                <ProductForm
                    product={editingProduct}
                    onSubmit={editingProduct.id ? handleUpdateProduct : handleCreateProduct}
                    onCancel={handleCancel}
                    categories={categories}
                    manufacturers={manufacturers}
                    maintenances={maintenances}
                />
            )}

            {isLoading && <div className="text-center my-4">Loading products...</div>}

            <ResourceTable
                title="Products"
                data={products.map((product) => ({
                    ...product,
                    id: product.id,
                    name: product.name,
                    article: product.article,
                    manufacturer_name: product.manufacturer_name,
                    price: product.prices?.USD || 0,
                }))}
                fields={['ID', 'Name', 'Article', 'Manufacturer_Name', 'Price']}
                onEdit={handleEditProduct}
                onDelete={handleDeleteProduct}
            />

            {pagination.last_page > 1 && renderPagination()}

            <h2 className="text-2xl font-bold mb-4 mt-8">Categories</h2>

            {loadingStates.categories && <div className="text-center my-4">Loading categories...</div>}

            {errors.categories && (
                <div className="mb-4 p-3 rounded bg-red-100 border border-red-400 text-red-700">
                    {errors.categories}
                    <button
                        onClick={fetchCategories}
                        className="ml-2 px-2 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600"
                    >
                        Retry
                    </button>
                </div>
            )}

            <button
                className="bg-blue-500 text-white p-2 rounded mb-4 hover:bg-blue-600 transition-colors"
                onClick={handleCreateCategoryClick}
            >
                Create Category
            </button>

            {editingCategory !== null && (
                <CategoryForm
                    category={editingCategory}
                    onSubmit={editingCategory.id ? handleUpdateCategory : handleCreateCategory}
                    onCancel={handleCancel}
                />
            )}

            <div className="mb-2 text-sm text-gray-600">
                Categories loaded: {categories.length}
            </div>

            <ResourceTable
                title="Categories"
                data={categories}
                fields={['ID', 'Name', 'Alias']}
                onEdit={handleEditCategory}
                onDelete={handleDeleteCategory}
            />

            <h2 className="text-2xl font-bold mb-4 mt-8">Manufacturers</h2>

            {loadingStates.manufacturers && <div className="text-center my-4">Loading manufacturers...</div>}

            {errors.manufacturers && (
                <div className="mb-4 p-3 rounded bg-red-100 border border-red-400 text-red-700">
                    {errors.manufacturers}
                    <button
                        onClick={fetchManufacturers}
                        className="ml-2 px-2 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600"
                    >
                        Retry
                    </button>
                </div>
            )}

            <button
                className="bg-blue-500 text-white p-2 rounded mb-4 hover:bg-blue-600 transition-colors"
                onClick={handleCreateManufacturerClick}
            >
                Create Manufacturer
            </button>

            {editingManufacturer !== null && (
                <ManufacturerForm
                    manufacturer={editingManufacturer}
                    onSubmit={editingManufacturer.id ? handleUpdateManufacturer : handleCreateManufacturer}
                    onCancel={handleCancel}
                />
            )}

            <div className="mb-2 text-sm text-gray-600">
                Manufacturers loaded: {manufacturers.length}
            </div>

            <ResourceTable
                title="Manufacturers"
                data={manufacturers}
                fields={['ID', 'Name']}
                onEdit={handleEditManufacturer}
                onDelete={handleDeleteManufacturer}
            />

            <h2 className="text-2xl font-bold mb-4 mt-8">Maintenances</h2>

            {loadingStates.maintenances && <div className="text-center my-4">Loading maintenances...</div>}

            {errors.maintenances && (
                <div className="mb-4 p-3 rounded bg-red-100 border border-red-400 text-red-700">
                    {errors.maintenances}
                    <button
                        onClick={fetchMaintenances}
                        className="ml-2 px-2 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600"
                    >
                        Retry
                    </button>
                </div>
            )}

            <button
                className="bg-blue-500 text-white p-2 rounded mb-4 hover:bg-blue-600 transition-colors"
                onClick={handleCreateMaintenanceClick}
            >
                Create Maintenance
            </button>

            {editingMaintenance !== null && (
                <MaintenanceForm
                    maintenance={editingMaintenance}
                    onSubmit={editingMaintenance.id ? handleUpdateMaintenance : handleCreateMaintenance}
                    onCancel={handleCancel}
                />
            )}

            <div className="mb-2 text-sm text-gray-600">
                Maintenances loaded: {maintenances.length}
            </div>

            <ResourceTable
                title="Maintenances"
                data={maintenances}
                fields={['ID', 'Name', 'Description', 'Duration']}
                onEdit={handleEditMaintenance}
                onDelete={handleDeleteMaintenance}
            />
        </div>
    );
};

export default AdminPanel;