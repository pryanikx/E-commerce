import { useState, useEffect } from 'react';
import api from '../api/api';
import CategoryProductList from './CategoryProductList';

const Home = () => {
    const [categories, setCategories] = useState([]);
    const [selectedCategory, setSelectedCategory] = useState(null);
    const [error, setError] = useState('');

    useEffect(() => {
        const fetchCategories = async () => {
            try {
                const response = await api.get('/categories');
                const categoriesData = Array.isArray(response.data) ? response.data : [];
                setCategories(categoriesData);
                if (categoriesData.length > 0) {
                    setSelectedCategory(categoriesData[0].id); // Select first category by default
                }
            } catch (error) {
                console.error('Failed to fetch categories:', error);
                setError('Failed to load categories. Please try again later.');
                setCategories([]);
            }
        };
        fetchCategories();
    }, []);

    const handleCategoryChange = (e) => {
        setSelectedCategory(e.target.value ? Number(e.target.value) : null);
    };

    return (
        <div className="container mx-auto p-4">
            <h1 className="text-3xl font-bold mb-8">Shop</h1>
            {error && <p className="text-red-500 mb-4">{error}</p>}
            <div className="mb-4">
                <label className="block text-sm font-medium mb-2">Select Category:</label>
                <select
                    value={selectedCategory || ''}
                    onChange={handleCategoryChange}
                    className="w-full max-w-xs p-2 border rounded"
                >
                    <option value="">Choose a category</option>
                    {categories.map((category) => (
                        <option key={category.id} value={category.id}>
                            {category.name}
                        </option>
                    ))}
                </select>
            </div>
            {selectedCategory && (
                <CategoryProductList categoryId={selectedCategory} />
            )}
        </div>
    );
};

export default Home;