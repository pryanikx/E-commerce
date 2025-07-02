import { useState, useEffect } from 'react';
import api from '../api/api';
import ProductCard from './ProductCard';

const CategoryProductList = ({ categoryId }) => {
    const [products, setProducts] = useState([]);
    const [error, setError] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [filterInputs, setFilterInputs] = useState({
        manufacturer_id: '',
        price_min: '',
        price_max: '',
        sort_by: 'id',
        sort_order: 'asc',
    });
    const [filters, setFilters] = useState({
        manufacturer_id: '',
        price_min: '',
        price_max: '',
        sort_by: 'id',
        sort_order: 'asc',
    });
    const [pagination, setPagination] = useState({
        current_page: 1,
        per_page: 20,
        total: 0,
        last_page: 1,
    });

    // Функция для очистки параметров от пустых значений
    const cleanParams = (params) => {
        const cleaned = {};
        Object.keys(params).forEach(key => {
            const value = params[key];
            // Исключаем пустые строки, null и undefined, но оставляем 0 и false
            if (value !== '' && value !== null && value !== undefined) {
                cleaned[key] = value;
            }
        });
        return cleaned;
    };

    const fetchProducts = async () => {
        setIsLoading(true);
        try {
            // Очищаем параметры от пустых значений перед отправкой
            const cleanedParams = cleanParams({
                ...filters,
                page: pagination.current_page,
            });

            const response = await api.get(`/categories/${categoryId}/products`, {
                params: cleanedParams,
                headers: { 'Cache-Control': 'no-cache' },
            });
            console.log('Request URL:', response.config.url + '?' + new URLSearchParams(response.config.params).toString());
            console.log('API Response for products:', response.data);
            setProducts(response.data.data || []);
            if (response.data.meta) {
                setPagination((prev) => ({
                    ...prev,
                    ...response.data.meta,
                }));
            }
            setError('');
        } catch (err) {
            console.error('Failed to fetch products:', err);
            setError('Failed to load products. Please try again.');
            setProducts([]);
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        fetchProducts();
    }, [categoryId, filters, pagination.current_page]);

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFilterInputs((prev) => ({
            ...prev,
            [name]: value,
        }));
    };

    const handleApplyFilters = () => {
        setFilters(filterInputs);
        setPagination((prev) => ({ ...prev, current_page: 1 })); // Reset to page 1 on filter change
    };

    const handlePageChange = (page) => {
        console.log('Changing page to:', page);
        setPagination((prev) => ({ ...prev, current_page: page }));
    };

    const renderPagination = () => {
        const pages = [];
        for (let i = 1; i <= pagination.last_page; i++) {
            pages.push(
                <button
                    key={i}
                    onClick={() => handlePageChange(i)}
                    disabled={isLoading}
                    className={`px-4 py-2 mx-1 border rounded ${
                        pagination.current_page === i ? 'bg-blue-500 text-white' : 'bg-white text-blue-500'
                    } ${isLoading ? 'opacity-50 cursor-not-allowed' : ''}`}
                >
                    {i}
                </button>
            );
        }
        return (
            <div className="flex justify-center mt-4 gap-2">
                <button
                    onClick={() => handlePageChange(pagination.current_page - 1)}
                    disabled={pagination.current_page === 1 || isLoading}
                    className={`px-4 py-2 border rounded bg-white text-blue-500 ${
                        pagination.current_page === 1 || isLoading ? 'opacity-50 cursor-not-allowed' : ''
                    }`}
                >
                    Previous
                </button>
                {pages}
                <button
                    onClick={() => handlePageChange(pagination.current_page + 1)}
                    disabled={pagination.current_page === pagination.last_page || isLoading}
                    className={`px-4 py-2 border rounded bg-white text-blue-500 ${
                        pagination.current_page === pagination.last_page || isLoading ? 'opacity-50 cursor-not-allowed' : ''
                    }`}
                >
                    Next
                </button>
            </div>
        );
    };

    return (
        <div className="container mx-auto p-4">
            <h2 className="text-2xl font-bold mb-4">Products</h2>
            {error && <p className="text-red-500 mb-4">{error}</p>}
            <div className="mb-6 p-4 bg-white rounded shadow">
                <h3 className="text-lg font-semibold mb-2">Filters & Sorting</h3>
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label className="block text-sm font-medium">Manufacturer ID</label>
                        <input
                            type="number"
                            name="manufacturer_id"
                            value={filterInputs.manufacturer_id}
                            onChange={handleInputChange}
                            className="w-full p-1 border rounded"
                            placeholder="Enter ID"
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium">Price Min (USD)</label>
                        <input
                            type="number"
                            name="price_min"
                            value={filterInputs.price_min}
                            onChange={handleInputChange}
                            className="w-full p-1 border rounded"
                            placeholder="Min price"
                            min="0"
                            step="0.01"
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium">Price Max (USD)</label>
                        <input
                            type="number"
                            name="price_max"
                            value={filterInputs.price_max}
                            onChange={handleInputChange}
                            className="w-full p-1 border rounded"
                            placeholder="Price max"
                            min="0"
                            step="0.01"
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium">Sort By</label>
                        <select
                            name="sort_by"
                            value={filterInputs.sort_by}
                            onChange={handleInputChange}
                            className="w-full p-1 border rounded"
                        >
                            <option value="id">ID</option>
                            <option value="price">Price</option>
                            <option value="release_date">Release Date</option>
                        </select>
                    </div>
                    <div>
                        <label className="block text-sm font-medium">Order</label>
                        <select
                            name="sort_order"
                            value={filterInputs.sort_order}
                            onChange={handleInputChange}
                            className="w-full p-1 border rounded"
                        >
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>
                        </select>
                    </div>
                </div>
                <button
                    onClick={handleApplyFilters}
                    className="mt-4 bg-blue-500 text-white p-2 rounded hover:bg-blue-600 transition-colors"
                >
                    Apply Filters & Sorting
                </button>
            </div>
            {isLoading && <div className="text-center my-4">Loading products...</div>}
            {products.length === 0 && !isLoading ? (
                <p className="text-gray-500">No products found.</p>
            ) : (
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    {products.map((product) => (
                        <ProductCard key={product.id} product={product} />
                    ))}
                </div>
            )}
            {pagination.last_page > 1 && renderPagination()}
        </div>
    );
};

export default CategoryProductList;