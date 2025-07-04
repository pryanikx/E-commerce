const ResourceTable = ({ title, data, fields, onEdit, onDelete }) => (
    <div className="mb-8">
        <h2 className="text-2xl font-bold mb-4">{title}</h2>
        <table className="w-full border-collapse border">
            <thead>
            <tr>
                {fields.map((field) => (
                    <th key={field} className="border p-2">{field}</th>
                ))}
                <th className="border p-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            {data.map((item) => (
                <tr key={item.id}>
                    {fields.map((field) => (
                        <td key={field} className="border p-2">{item[field.toLowerCase()]}</td>
                    ))}
                    <td className="border p-2">
                        <button onClick={() => onEdit(item)} className="text-blue-500 mr-2">Edit</button>
                        <button onClick={() => onDelete(item.id)} className="text-red-500">Delete</button>
                    </td>
                </tr>
            ))}
            </tbody>
        </table>
    </div>
);

export default ResourceTable;