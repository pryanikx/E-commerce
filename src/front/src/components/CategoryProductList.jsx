import { useState, useEffect } from 'react';
import api from '../api/api';
import ProductCard from './ProductCard';

const CategoryProductList = ({ categoryId }) => {
    const [products, setProducts] = useState([]);
    const [error, setError] = useState('');
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

    const fetchProducts = async () => {
        try {
            const response = await api.get(`/categories/${categoryId}/products`, {
                params: filters,
            });
            setProducts(response.data.data || []);
            setError('');
        } catch (err) {
            console.error('Failed to fetch products:', err);
            setError('Failed to load products. Please try again.');
            setProducts([]);
        }
    };

    useEffect(() => {
        fetchProducts();
    }, [categoryId]);

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFilterInputs((prev) => ({
            ...prev,
            [name]: value,
        }));
    };

    const handleApplyFilters = () => {
        setFilters(filterInputs);
        fetchProducts();
    };

    return (
        <div>
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
                            placeholder="Max price"
                            min="0"
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
                    className="mt-4 bg-primary text-white p-2 rounded"
                >
                    Apply Filters & Sorting
                </button>
            </div>
            {products.length === 0 ? (
                <p className="text-secondary">No products found.</p>
            ) : (
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    {products.map((product) => (
                        <ProductCard key={product.id} product={product} />
                    ))}
                </div>
            )}
        </div>
    );
};

export default CategoryProductList;