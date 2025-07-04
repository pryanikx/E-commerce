import { useState } from 'react';

const ManufacturerForm = ({ manufacturer, onSubmit, onCancel }) => {
    const [formData, setFormData] = useState({
        name: manufacturer?.name || '',
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
            setFormData({ name: '' });
        } catch (error) {
            console.error('Manufacturer form submission error:', error);
            if (error.response?.data?.errors) {
                setErrors(error.response.data.errors);
            }
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4 max-w-md bg-white p-6 rounded shadow">
            <h2 className="text-xl font-bold">
                {manufacturer && manufacturer.id ? 'Edit Manufacturer' : 'Create Manufacturer'}
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
            <div className="flex space-x-2">
                <button
                    type="submit"
                    className="bg-primary text-white p-2 rounded w-full"
                >
                    {manufacturer && manufacturer.id ? 'Update Manufacturer' : 'Create Manufacturer'}
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

export default ManufacturerForm;