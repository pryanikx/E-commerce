import { useState, useEffect } from 'react';
import api from '../api/api';

const ProductForm = ({ product, onSubmit, onCancel }) => {
    const [formData, setFormData] = useState({
        name: product?.name || '',
        article: product?.article || '',
        description: product?.description || '',
        release_date: product?.release_date || '',
        price: product?.price || '',
        manufacturer_id: product?.manufacturer_id || '',
        category_id: product?.category_id || '',
        maintenance_ids: product?.maintenances?.map(m => ({ id: m.id, price: m.pivot?.price || '' })) || [],
        image: null,
    });
    const [errors, setErrors] = useState({});
    const [isLoading, setIsLoading] = useState(false);
    const [manufacturers, setManufacturers] = useState([]);
    const [categories, setCategories] = useState([]);
    const [maintenances, setMaintenances] = useState([]);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const [manRes, catRes, mainRes] = await Promise.all([
                    api.get('/admin/manufacturers'),
                    api.get('/admin/categories'),
                    api.get('/admin/maintenance'),
                ]);
                setManufacturers(manRes.data.data || manRes.data);
                setCategories(catRes.data.data || catRes.data);
                setMaintenances(mainRes.data.data || mainRes.data);
            } catch (error) {
                console.error('Failed to fetch form data:', error);
            }
        };
        fetchData();
    }, []);

    const handleChange = (e) => {
        const { name, value, files } = e.target;
        if (name === 'image') {
            setFormData({ ...formData, image: files[0] });
        } else {
            setFormData({ ...formData, [name]: value });
        }
    };

    const handleMaintenanceChange = (index, field, value) => {
        const newMaintenances = [...formData.maintenance_ids];
        newMaintenances[index][field] = value;
        setFormData({ ...formData, maintenance_ids: newMaintenances });
    };

    const addMaintenance = () => {
        setFormData({
            ...formData,
            maintenance_ids: [...formData.maintenance_ids, { id: '', price: '' }],
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrors({});
        setIsLoading(true);

        // Формируем JSON-объект
        const jsonData = {
            name: formData.name || null,
            article: formData.article || null,
            description: formData.description || null,
            release_date: formData.release_date || null,
            price: formData.price ? parseFloat(formData.price) : null,
            image: null,
            manufacturer_id: formData.manufacturer_id ? parseInt(formData.manufacturer_id) : null,
            category_id: formData.category_id ? parseInt(formData.category_id) : null,
            maintenance_ids: formData.maintenance_ids
                .filter(m => m.id && m.price)
                .map(m => ({
                    id: parseInt(m.id),
                    price: parseFloat(m.price),
                })),
        };

        let dataToSend;
        let headers = { 'Content-Type': 'application/json' };
        if (formData.image) {
            dataToSend = new FormData();
            dataToSend.append('image', formData.image);
            // Добавляем остальные поля как отдельные
            Object.entries(jsonData).forEach(([key, value]) => {
                if (key === 'maintenance_ids') {
                    value.forEach((m, index) => {
                        dataToSend.append(`maintenance_ids[${index}][id]`, m.id);
                        dataToSend.append(`maintenance_ids[${index}][price]`, m.price);
                    });
                } else if (value !== null) {
                    dataToSend.append(key, value);
                }
            });
            headers = { 'Content-Type': 'multipart/form-data' };
        } else {
            dataToSend = jsonData;
        }

        // Логируем данные
        console.log('Submitting data:', formData.image ? Object.fromEntries(dataToSend) : jsonData);

        try {
            await onSubmit(dataToSend, headers);
            setFormData({
                name: '',
                article: '',
                description: '',
                release_date: '',
                price: '',
                manufacturer_id: '',
                category_id: '',
                maintenance_ids: [],
                image: null,
            });
            console.log('Form submitted successfully');
        } catch (error) {
            console.error('Form submission error:', {
                message: error.message,
                response: error.response?.data,
                status: error.response?.status,
            });
            if (error.response?.data?.errors) {
                setErrors(error.response.data.errors);
            } else {
                setErrors({ general: 'Failed to submit form. Please try again.' });
            }
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4 max-w-md bg-white p-6 rounded shadow">
            <h2 className="text-xl font-bold">
                {product && product.id ? 'Edit Product' : 'Create Product'}
            </h2>
            {Object.keys(errors).map((key) => (
                <p key={key} className="text-red-500">{errors[key][0] || errors[key]}</p>
            ))}
            <div>
                <label className="block text-sm font-medium">Name</label>
                <input
                    type="text"
                    name="name"
                    value={formData.name}
                    onChange={handleChange}
                    className="w-full p-2 border rounded"
                    required
                />
            </div>
            <div>
                <label className="block text-sm font-medium">Article</label>
                <input
                    type="text"
                    name="article"
                    value={formData.article}
                    onChange={handleChange}
                    className="w-full p-2 border rounded"
                    required
                />
            </div>
            <div>
                <label className="block text-sm font-medium">Description</label>
                <textarea
                    name="description"
                    value={formData.description}
                    onChange={handleChange}
                    className="w-full p-2 border rounded"
                />
            </div>
            <div>
                <label className="block text-sm font-medium">Release Date</label>
                <input
                    type="date"
                    name="release_date"
                    value={formData.release_date}
                    onChange={handleChange}
                    className="w-full p-2 border rounded"
                    required
                />
            </div>
            <div>
                <label className="block text-sm font-medium">Price</label>
                <input
                    type="number"
                    name="price"
                    value={formData.price}
                    onChange={handleChange}
                    className="w-full p-2 border rounded"
                    step="0.01"
                    required
                />
            </div>
            <div>
                <label className="block text-sm font-medium">Image</label>
                <input
                    type="file"
                    name="image"
                    onChange={handleChange}
                    className="w-full p-2 border rounded"
                    accept="image/jpeg,image/png"
                />
            </div>
            <div>
                <label className="block text-sm font-medium">Manufacturer</label>
                <select
                    name="manufacturer_id"
                    value={formData.manufacturer_id}
                    onChange={handleChange}
                    className="w-full p-2 border rounded"
                    required
                >
                    <option value="">Select Manufacturer</option>
                    {manufacturers.map((m) => (
                        <option key={m.id} value={m.id}>{m.name}</option>
                    ))}
                </select>
            </div>
            <div>
                <label className="block text-sm font-medium">Category</label>
                <select
                    name="category_id"
                    value={formData.category_id}
                    onChange={handleChange}
                    className="w-full p-2 border rounded"
                    required
                >
                    <option value="">Select Category</option>
                    {categories.map((c) => (
                        <option key={c.id} value={c.id}>{c.name}</option>
                    ))}
                </select>
            </div>
            <div>
                <label className="block text-sm font-medium">Maintenances</label>
                {formData.maintenance_ids.map((m, index) => (
                    <div key={index} className="flex space-x-2 mb-2">
                        <select
                            value={m.id}
                            onChange={(e) => handleMaintenanceChange(index, 'id', e.target.value)}
                            className="w-full p-2 border rounded"
                        >
                            <option value="">Select Maintenance</option>
                            {maintenances.map((main) => (
                                <option key={main.id} value={main.id}>{main.name}</option>
                            ))}
                        </select>
                        <input
                            type="number"
                            value={m.price}
                            onChange={(e) => handleMaintenanceChange(index, 'price', e.target.value)}
                            placeholder="Price"
                            className="w-full p-2 border rounded"
                            step="0.01"
                        />
                    </div>
                ))}
                <button
                    type="button"
                    onClick={addMaintenance}
                    className="text-blue-500 hover:underline"
                >
                    Add Maintenance
                </button>
            </div>
            <div className="flex space-x-2">
                <button
                    type="submit"
                    className="bg-primary text-white p-2 rounded w-full"
                    disabled={isLoading}
                >
                    {isLoading ? 'Saving...' : (product && product.id ? 'Update Product' : 'Create Product')}
                </button>
                <button
                    type="button"
                    onClick={onCancel}
                    className="bg-secondary text-white p-2 rounded w-full"
                    disabled={isLoading}
                >
                    Cancel
                </button>
            </div>
        </form>
    );
};

export default ProductForm;