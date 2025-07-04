import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../api/api';

const ProductDetails = () => {
    const { id } = useParams();
    const [product, setProduct] = useState(null);
    const [error, setError] = useState('');
    const [selectedMaintenances, setSelectedMaintenances] = useState([]);
    const [selectedCurrency, setSelectedCurrency] = useState('USD');

    useEffect(() => {
        const fetchProduct = async () => {
            try {
                const response = await api.get(`/products/${id}`);
                setProduct(response.data);
                if (response.data.prices) {
                    setSelectedCurrency(Object.keys(response.data.prices)[0]);
                }
            } catch (err) {
                setError('Failed to load product details.');
                console.error('Error fetching product:', err);
            }
        };
        fetchProduct();
    }, [id]);

    const handleMaintenanceToggle = (maintenanceIndex) => {
        setSelectedMaintenances(prev => {
            if (prev.includes(maintenanceIndex)) {
                return prev.filter(index => index !== maintenanceIndex);
            } else {
                return [...prev, maintenanceIndex];
            }
        });
    };

    const calculateTotalPrice = () => {
        if (!product || !product.prices) return 0;

        let total = product.prices[selectedCurrency] || 0;

        selectedMaintenances.forEach(maintenanceIndex => {
            const maintenance = product.maintenances[maintenanceIndex];
            if (maintenance && maintenance.prices && maintenance.prices[selectedCurrency]) {
                total += maintenance.prices[selectedCurrency];
            }
        });

        return total;
    };

    const formatPrice = (price) => {
        return price.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

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

    const availableCurrencies = product.prices ? Object.keys(product.prices) : [];

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

                {/* Currency Selector */}
                {availableCurrencies.length > 1 && (
                    <div className="mb-4">
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                            Select Currency:
                        </label>
                        <select
                            value={selectedCurrency}
                            onChange={(e) => setSelectedCurrency(e.target.value)}
                            className="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            {availableCurrencies.map(currency => (
                                <option key={currency} value={currency}>
                                    {currency}
                                </option>
                            ))}
                        </select>
                    </div>
                )}

                <p className="text-secondary mb-2">
                    <strong>Base Price:</strong> {selectedCurrency} {formatPrice(product.prices[selectedCurrency])}
                </p>

                <p className="text-secondary mb-2">
                    <strong>Release Date:</strong> {product.release_date}
                </p>
                <p className="text-secondary mb-4">
                    <strong>Description:</strong> {product.description || 'No description available'}
                </p>

                {/* Maintenance Selection */}
                {product.maintenances && product.maintenances.length > 0 && (
                    <div className="mb-6">
                        <h2 className="text-xl font-bold mb-3">Available Maintenance Services</h2>
                        <p className="text-sm text-gray-600 mb-3">Select the maintenance services you want to include:</p>

                        <div className="space-y-3">
                            {product.maintenances.map((maintenance, index) => (
                                <div key={index} className="flex items-center space-x-3 p-3 border border-gray-200 rounded">
                                    <input
                                        type="checkbox"
                                        id={`maintenance-${index}`}
                                        checked={selectedMaintenances.includes(index)}
                                        onChange={() => handleMaintenanceToggle(index)}
                                        className="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                    <label
                                        htmlFor={`maintenance-${index}`}
                                        className="flex-1 cursor-pointer"
                                    >
                                        <span className="font-medium capitalize">{maintenance.name}</span>
                                        {maintenance.prices && maintenance.prices[selectedCurrency] && (
                                            <span className="ml-2 text-gray-600">
                                                (+{selectedCurrency} {formatPrice(maintenance.prices[selectedCurrency])})
                                            </span>
                                        )}
                                    </label>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* Total Price Summary */}
                <div className="bg-gray-50 p-4 rounded border-t-4 border-blue-500">
                    <h3 className="text-lg font-bold mb-2">Price Summary</h3>

                    <div className="space-y-1 text-sm">
                        <div className="flex justify-between">
                            <span>Base Product Price:</span>
                            <span>{selectedCurrency} {formatPrice(product.prices[selectedCurrency])}</span>
                        </div>

                        {selectedMaintenances.length > 0 && (
                            <>
                                <div className="font-medium mt-2">Selected Maintenance:</div>
                                {selectedMaintenances.map(index => (
                                    <div key={index} className="flex justify-between pl-4">
                                        <span className="capitalize">{product.maintenances[index].name}:</span>
                                        <span>
                                            {selectedCurrency} {formatPrice(product.maintenances[index].prices[selectedCurrency])}
                                        </span>
                                    </div>
                                ))}
                            </>
                        )}

                        <div className="border-t pt-2 mt-2">
                            <div className="flex justify-between font-bold text-lg">
                                <span>Total Price:</span>
                                <span className="text-blue-600">
                                    {selectedCurrency} {formatPrice(calculateTotalPrice())}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ProductDetails;