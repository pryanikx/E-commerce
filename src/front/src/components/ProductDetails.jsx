import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../api/api';

const ProductDetails = () => {
    const { id } = useParams();
    const [product, setProduct] = useState(null);
    const [error, setError] = useState('');

    useEffect(() => {
        const fetchProduct = async () => {
            try {
                const response = await api.get(`/products/${id}`);
                setProduct(response.data);
            } catch (err) {
                setError('Failed to load product details.');
                console.error('Error fetching product:', err);
            }
        };
        fetchProduct();
    }, [id]);

    if (error) {
        return (
            <div className="container mx-auto p-4">
                <p className="text-red-500">{error}</p>
                <Link to="/" className="text-primary hover:underline">
                    Back to Home
                </Link>
            </div>
        );
    }

    if (!product) {
        return <div className="container mx-auto p-4">Loading...</div>;
    }

    return (
        <div className="container mx-auto p-4">
            <Link to="/" className="text-primary hover:underline mb-4 inline-block">
                Back to Home
            </Link>
            <div className="bg-white p-6 rounded shadow">
                <h1 className="text-3xl font-bold mb-4">{product.name}</h1>
                <img
                    src={product.image_url}
                    alt={product.name}
                    className="w-full max-w-md h-auto mb-4 rounded"
                />
                <p className="text-secondary mb-2">
                    <strong>Article:</strong> {product.article}
                </p>
                <p className="text-secondary mb-2">
                    <strong>Category:</strong> {product.category_name}
                </p>
                <p className="text-secondary mb-2">
                    <strong>Manufacturer:</strong> {product.manufacturer_name}
                </p>
                <p className="text-secondary mb-2">
                    <strong>Prices:</strong>
                    {product.prices && (
                        <span className="ml-2">
                            {Object.entries(product.prices).map(([currency, price]) => (
                                <span key={currency} className="mr-2">
                                    {currency}: {price.toLocaleString('en-US', { maximumFractionDigits: 2 })}
                                </span>
                            ))}
                        </span>
                    )}
                </p>
                <p className="text-secondary mb-2">
                    <strong>Release Date:</strong> {product.release_date}
                </p>
                <p className="text-secondary mb-4">
                    <strong>Description:</strong> {product.description || 'No description available'}
                </p>
                {product.maintenances && product.maintenances.length > 0 && (
                    <div>
                        <h2 className="text-xl font-bold mb-2">Maintenances</h2>
                        <ul className="list-disc pl-5">
                            {product.maintenances.map((maintenance, index) => (
                                <li key={index} className="text-secondary">
                                    {maintenance.name}:
                                    {maintenance.prices && (
                                        <span className="ml-2">
                                            {Object.entries(maintenance.prices).map(([currency, price]) => (
                                                <span key={currency} className="mr-2">
                                                    {currency}: {price.toLocaleString('en-US', { maximumFractionDigits: 2 })}
                                                </span>
                                            ))}
                                        </span>
                                    )}
                                </li>
                            ))}
                        </ul>
                    </div>
                )}
            </div>
        </div>
    );
};

export default ProductDetails;