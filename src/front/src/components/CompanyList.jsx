import { useState } from 'react';
import PropTypes from 'prop-types';

function CompanyList({ companies, onAddCompany, onUpdateCompany, onDeleteCompany }) {
    const [newCompanyName, setNewCompanyName] = useState('');
    const [editCompanyId, setEditCompanyId] = useState(null);
    const [editCompanyName, setEditCompanyName] = useState('');

    const handleAddCompany = () => {
        if (!newCompanyName.trim()) return;
        onAddCompany(newCompanyName);
        setNewCompanyName('');
    };

    const handleUpdateCompany = (id) => {
        if (!editCompanyName) return;
        onUpdateCompany(id, editCompanyName);
        setEditCompanyId(null);
        setEditCompanyName('');
    };

    return (
        <div>
            <div className="mb-6">
                <h3 className="text-lg font-semibold mb-2">Add New Company</h3>
                <div className="flex max-w-md">
                    <input
                        type="text"
                        value={newCompanyName}
                        onChange={(e) => setNewCompanyName(e.target.value)}
                        placeholder="Enter company name"
                        className="w-full p-2 border rounded mr-2"
                    />
                    <button
                        onClick={handleAddCompany}
                        className="bg-green-600 text-white p-2 rounded"
                    >
                        Add
                    </button>
                </div>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                {companies.map((company) => (
                    <div key={company.id} className="bg-white p-4 rounded shadow">
                        {editCompanyId === company.id ? (
                            <div className="flex flex-col">
                                <input
                                    type="text"
                                    value={editCompanyName}
                                    onChange={(e) => setEditCompanyName(e.target.value)}
                                    className="p-2 border rounded mb-2"
                                />
                                <div>
                                    <button
                                        onClick={() => handleUpdateCompany(company.id)}
                                        className="bg-blue-600 text-white p-2 rounded mr-2"
                                    >
                                        Save
                                    </button>
                                    <button
                                        onClick={() => setEditCompanyId(null)}
                                        className="bg-gray-600 text-white p-2 rounded"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        ) : (
                            <>
                                <h3 className="text-lg font-semibold">{company.name}</h3>
                                <div className="mt-2">
                                    <button
                                        onClick={() => {
                                            setEditCompanyId(company.id);
                                            setEditCompanyName(company.name);
                                        }}
                                        className="bg-yellow-600 text-white p-2 rounded mr-2"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        onClick={() => onDeleteCompany(company.id)}
                                        className="bg-red-600 text-white p-2 rounded"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </>
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
}

CompanyList.propTypes = {
    companies: PropTypes.array.isRequired,
    onAddCompany: PropTypes.func.isRequired,
    onUpdateCompany: PropTypes.func.isRequired,
    onDeleteCompany: PropTypes.func.isRequired,
};

export default CompanyList;