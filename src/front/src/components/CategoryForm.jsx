import { useState } from 'react';

const CategoryForm = ({ category, onSubmit, onCancel }) => {
    const [formData, setFormData] = useState({
        name: category?.name || '',
        alias: category?.alias || '',
    });
    const [errors, setErrors] = useState({});

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrors({});
        try {
            await onSubmit(formData);
            setFormData({ name: '', alias: '' });
        } catch (error) {
            console.error('Category form submission error:', error);
            if (error.response?.data?.errors) {
                setErrors(error.response.data.errors);
            }
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4 max-w-md bg-white p-6 rounded shadow">
            <h2 className="text-xl font-bold">
                {category && category.id ? 'Edit Category' : 'Create Category'}
            </h2>
            {Object.keys(errors).map((key) => (
                <p key={key} className="text-red-500">{errors[key][0]}</p>
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
                <label className="block text-sm font-medium">Alias</label>
                <input
                    type="text"
                    name="alias"
                    value={formData.alias}
                    onChange={handleChange}
                    className="w-full p-2 border rounded"
                    required
                />
            </div>
            <div className="flex space-x-2">
                <button
                    type="submit"
                    className="bg-primary text-white p-2 rounded w-full"
                >
                    {category && category.id ? 'Update Category' : 'Create Category'}
                </button>
                <button
                    type="button"
                    onClick={onCancel}
                    className="bg-secondary text-white p-2 rounded w-full"
                >
                    Cancel
                </button>
            </div>
        </form>
    );
};

export default CategoryForm;