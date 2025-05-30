import { useState, useEffect } from 'react';
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

    useEffect(() => {
        const fetchData = async () => {
            try {
                const [prodRes, catRes, manRes, mainRes] = await Promise.all([
                    api.get('/admin/products'),
                    api.get('/admin/categories'),
                    api.get('/admin/manufacturers'),
                    api.get('/admin/maintenance'),
                ]);
                setProducts(prodRes.data.data || prodRes.data);
                setCategories(catRes.data.data || catRes.data);
                setManufacturers(manRes.data.data || manRes.data);
                setMaintenances(mainRes.data.data || mainRes.data);
                console.log('Data fetched successfully');
            } catch (error) {
                console.error('Failed to fetch data:', error);
            }
        };
        fetchData();
    }, []);

    const handleCreateProduct = async (data, headers) => {
        if (!data) {
            setEditingProduct(null);
            return;
        }
        try {
            const response = await api.post('/admin/products', data, { headers });
            console.log('Create product response:', response.data);
            const productsRes = await api.get('/admin/products');
            setProducts(productsRes.data.data || productsRes.data);
            setEditingProduct(null);
            console.log('Product created successfully');
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
            const response = await api.put(`/admin/products/${editingProduct.id}`, data, { headers });
            console.log('Update product response:', response.data);
            const productsRes = await api.get('/admin/products');
            setProducts(productsRes.data.data || productsRes.data);
            setEditingProduct(null);
            console.log('Product updated successfully');
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
            setProducts(products.filter((p) => p.id !== id));
            console.log('Product deleted successfully');
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
            const response = await api.get('/admin/categories');
            setCategories(response.data.data || response.data);
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
            const response = await api.get('/admin/categories');
            setCategories(response.data.data || response.data);
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
            const response = await api.get('/admin/manufacturers');
            setManufacturers(response.data.data || response.data);
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
            const response = await api.get('/admin/manufacturers');
            setManufacturers(response.data.data || response.data);
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
            const response = await api.get('/admin/maintenance');
            setMaintenances(response.data.data || response.data);
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
            const response = await api.get('/admin/maintenance');
            setMaintenances(response.data.data || response.data);
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
            console.error('Failed to delete maintenance:', error);
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

    return (
        <div className="container mx-auto p-4">
            <h1 className="text-3xl font-bold mb-8">Admin Panel</h1>

            <h2 className="text-2xl font-bold mb-4">Products</h2>
            <button
                onClick={handleCreateProductClick}
                className="bg-primary text-white p-2 rounded mb-4"
            >
                Create Product
            </button>
            {editingProduct !== null && (
                <ProductForm
                    product={editingProduct}
                    onSubmit={editingProduct.id ? handleUpdateProduct : handleCreateProduct}
                    onCancel={handleCancel}
                />
            )}
            <ResourceTable
                title="Products"
                data={products}
                fields={['ID', 'Name', 'Article', 'Manufacturer_Name', 'Price']}
                onEdit={handleEditProduct}
                onDelete={handleDeleteProduct}
            />

            <h2 className="text-2xl font-bold mb-4">Categories</h2>
            <button
                onClick={handleCreateCategoryClick}
                className="bg-primary text-white p-2 rounded mb-4"
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
            <ResourceTable
                title="Categories"
                data={categories}
                fields={['ID', 'Name', 'Alias']}
                onEdit={handleEditCategory}
                onDelete={handleDeleteCategory}
            />

            <h2 className="text-2xl font-bold mb-4">Manufacturers</h2>
            <button
                onClick={handleCreateManufacturerClick}
                className="bg-primary text-white p-2 rounded mb-4"
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
            <ResourceTable
                title="Manufacturers"
                data={manufacturers}
                fields={['ID', 'Name']}
                onEdit={handleEditManufacturer}
                onDelete={handleDeleteManufacturer}
            />

            <h2 className="text-2xl font-bold mb-4">Maintenances</h2>
            <button
                onClick={handleCreateMaintenanceClick}
                className="bg-primary text-white p-2 rounded mb-4"
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