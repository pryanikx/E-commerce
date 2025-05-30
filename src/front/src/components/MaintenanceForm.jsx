import { useState } from 'react';

const MaintenanceForm = ({ maintenance, onSubmit, onCancel }) => {
    const [formData, setFormData] = useState({
        name: maintenance?.name || '',
        description: maintenance?.description || '',
        duration: maintenance?.duration || '',
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
            setFormData({ name: '', description: '', duration: '' });
        } catch (error) {
            console.error('Maintenance form submission error:', error);
            if (error.response?.data?.errors) {
                setErrors(error.response.data.errors);
            }
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4 max-w-md bg-white p-6 rounded shadow">
            <h2 className="text-xl font-bold">
                {maintenance && maintenance.id ? 'Edit Maintenance' : 'Create Maintenance'}
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
                <label className="block text-sm font-medium">Description</label>
                <textarea
                    name="description"
                    value={formData.description}
                    onChange={handleChange}
                    className="w-full p-2 border rounded"
                />
            </div>
            <div>
                <label className="block text-sm font-medium">Duration</label>
                <input
                    type="text"
                    name="duration"
                    value={formData.duration}
                    onChange={handleChange}
                    className="w-full p-2 border rounded"
                />
            </div>
            <div className="flex space-x-2">
                <button
                    type="submit"
                    className="bg-primary text-white p-2 rounded w-full"
                >
                    {maintenance && maintenance.id ? 'Update Maintenance' : 'Create Maintenance'}
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

export default MaintenanceForm;