import { useState, useEffect } from 'react';
import api from '../api/api';
import { FALLBACK_IMAGE_URL } from '../constants';

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

    const removeMaintenance = (index) => {
        const newMaintenances = formData.maintenance_ids.filter((_, i) => i !== index);
        setFormData({ ...formData, maintenance_ids: newMaintenances });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrors({});
        setIsLoading(true);

        try {
            let dataToSend;
            let headers = {};

            console.log('Form data before submit:', formData);
            console.log('Has image:', !!formData.image);
            console.log('Is editing:', !!(product && product.id));

            if (formData.image) {
                console.log('Using FormData for image upload');
                dataToSend = new FormData();

                dataToSend.append('name', formData.name || '');
                dataToSend.append('article', formData.article || '');
                dataToSend.append('description', formData.description || '');
                dataToSend.append('release_date', formData.release_date || '');
                dataToSend.append('price', formData.price || '');
                dataToSend.append('manufacturer_id', formData.manufacturer_id || '');
                dataToSend.append('category_id', formData.category_id || '');

                dataToSend.append('image', formData.image);

                formData.maintenance_ids
                    .filter(m => m.id && m.price)
                    .forEach((m, index) => {
                        dataToSend.append(`maintenance_ids[${index}][id]`, m.id);
                        dataToSend.append(`maintenance_ids[${index}][price]`, m.price);
                    });

                headers = {};

                for (let [key, value] of dataToSend.entries()) {
                    console.log(`FormData ${key}:`, value);
                }
            } else {
                console.log('Using JSON for data without image');
                dataToSend = {};

                if (formData.name) dataToSend.name = formData.name;
                if (formData.article) dataToSend.article = formData.article;
                if (formData.description) dataToSend.description = formData.description;
                if (formData.release_date) dataToSend.release_date = formData.release_date;
                if (formData.price) dataToSend.price = parseFloat(formData.price);
                if (formData.manufacturer_id) dataToSend.manufacturer_id = parseInt(formData.manufacturer_id);
                if (formData.category_id) dataToSend.category_id = parseInt(formData.category_id);

                const validMaintenances = formData.maintenance_ids.filter(m => m.id && m.price);
                if (validMaintenances.length > 0) {
                    dataToSend.maintenance_ids = validMaintenances.map(m => ({
                        id: parseInt(m.id),
                        price: parseFloat(m.price),
                    }));
                }

                headers = { 'Content-Type': 'application/json' };
            }

            console.log('Final data to send:', dataToSend instanceof FormData ? 'FormData (see above logs)' : dataToSend);
            console.log('Headers:', headers);

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

            const fileInput = document.querySelector('input[type="file"]');
            if (fileInput) fileInput.value = '';

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
                setErrors({ general: error.response?.data?.message || 'Failed to submit form. Please try again.' });
            }
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4 max-w-2xl bg-white p-6 rounded shadow">
            <h2 className="text-xl font-bold">
                {product && product.id ? 'Edit Product' : 'Create Product'}
            </h2>

            {errors.general && (
                <div className="p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {errors.general}
                </div>
            )}

            {Object.keys(errors).filter(key => key !== 'general').map((key) => (
                <p key={key} className="text-red-500 text-sm">
                    {key}: {Array.isArray(errors[key]) ? errors[key][0] : errors[key]}
                </p>
            ))}

            <div>
                <label className="block text-sm font-medium">Name *</label>
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
                <label className="block text-sm font-medium">Article *</label>
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
                    rows="3"
                />
            </div>

            <div>
                <label className="block text-sm font-medium">Release Date *</label>
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
                <label className="block text-sm font-medium">Price *</label>
                <input
                    type="number"
                    name="price"
                    value={formData.price}
                    onChange={handleChange}
                    className="w-full p-2 border rounded"
                    step="0.01"
                    min="0"
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
                    accept="image/jpeg,image/png,image/jpg"
                />
                <p className="text-xs text-gray-500 mt-1">
                    Max size: 2MB. Formats: JPEG, PNG, JPG
                </p>
                {product?.image_url && (
                    <div className="mt-2">
                        <p className="text-sm text-gray-600">Current image:</p>
                        <img
                            src={product.image_url ?? FALLBACK_IMAGE_URL}
                            alt="Current product image"
                            className="w-20 h-20 object-cover border rounded"
                            onError={(e) => {
                                e.currentTarget.src = FALLBACK_IMAGE_URL;
                            }}
                        />
                    </div>
                )}
            </div>

            <div>
                <label className="block text-sm font-medium">Manufacturer *</label>
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
                <label className="block text-sm font-medium">Category *</label>
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
                            className="flex-1 p-2 border rounded"
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
                            className="flex-1 p-2 border rounded"
                            step="0.01"
                            min="0"
                        />
                        <button
                            type="button"
                            onClick={() => removeMaintenance(index)}
                            className="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                        >
                            Remove
                        </button>
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
                    className="bg-blue-500 text-white p-2 rounded w-full hover:bg-blue-600 transition-colors"
                    disabled={isLoading}
                >
                    {isLoading ? 'Saving...' : (product && product.id ? 'Update Product' : 'Create Product')}
                </button>
                <button
                    type="button"
                    onClick={onCancel}
                    className="bg-gray-500 text-white p-2 rounded w-full hover:bg-gray-600 transition-colors"
                    disabled={isLoading}
                >
                    Cancel
                </button>
            </div>
        </form>
    );
};

export default ProductForm;