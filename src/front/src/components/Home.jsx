import { useState, useEffect } from 'react';
import api from '../api/api';
import CategoryCard from './CategoryCard';

const Home = () => {
    const [categories, setCategories] = useState([]);
    const [productsByCategory, setProductsByCategory] = useState({});
    const [error, setError] = useState('');

    useEffect(() => {
        const fetchData = async () => {
            try {
                // Получаем категории
                const categoriesResponse = await api.get('/categories');
                const categoriesData = Array.isArray(categoriesResponse.data)
                    ? categoriesResponse.data
                    : [];
                console.log('Categories:', categoriesData);
                setCategories(categoriesData);

                // Получаем продукты для каждой категории
                const productsData = {};
                for (const category of categoriesData) {
                    try {
                        const response = await api.get(`/categories/${category.id}/products`);
                        // Извлекаем массив из response.data.data
                        productsData[category.id] = Array.isArray(response.data.data)
                            ? response.data.data
                            : [];
                        console.log(`Products for category ${category.id}:`, productsData[category.id]);
                    } catch (error) {
                        console.error(`Failed to fetch products for category ${category.id}:`, error);
                        productsData[category.id] = [];
                    }
                }
                setProductsByCategory(productsData);
            } catch (error) {
                console.error('Failed to fetch categories:', error);
                setError('Failed to load data. Please try again later.');
                setCategories([]);
                setProductsByCategory({});
            }
        };
        fetchData();
    }, []);

    return (
        <div className="container mx-auto p-4">
            <h1 className="text-3xl font-bold mb-8">Categories</h1>
            {error && <p className="text-red-500 mb-4">{error}</p>}
            {categories.length === 0 ? (
                <p className="text-secondary">No categories available</p>
            ) : (
                categories.map((category) => (
                    <CategoryCard
                        key={category.id}
                        category={category}
                        products={productsByCategory[category.id] || []}
                    />
                ))
            )}
        </div>
    );
};

export default Home;