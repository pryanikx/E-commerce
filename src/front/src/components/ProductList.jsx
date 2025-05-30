import PropTypes from 'prop-types';
import { useNavigate } from 'react-router-dom';

function ProductList({ products, onSelectProduct, onDeleteProduct }) {
    const navigate = useNavigate();

    return (
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            {products.map((product) => (
                <div key={product.article} className="bg-white p-4 rounded shadow">
                    <div
                        className="cursor-pointer hover:bg-gray-100 p-2"
                        onClick={() => onSelectProduct(product)}
                    >
                        <h3 className="text-lg font-semibold">{product.name || 'No Name'}</h3>
                        <p><strong>Manufacturer:</strong> {product.manufacturer_name || 'N/A'}</p>
                        <p><strong>Price:</strong> ${product.price?.toFixed(2) || '0.00'}</p>
                        {product.image_url && (
                            <img
                                src={product.image_url}
                                alt={product.name}
                                className="max-w-full h-32 object-cover mt-2 rounded"
                            />
                        )}
                    </div>
                    <div className="mt-2 flex justify-between">
                        <button
                            onClick={() => navigate(`/admin/edit/${product.id}`)}
                            className="bg-yellow-600 text-white p-2 rounded mr-2"
                        >
                            Edit
                        </button>
                        <button
                            onClick={() => onDeleteProduct(product.id)}
                            className="bg-red-600 text-white p-2 rounded"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            ))}
        </div>
    );
}

ProductList.propTypes = {
    products: PropTypes.array.isRequired,
    onSelectProduct: PropTypes.func.isRequired,
    onDeleteProduct: PropTypes.func.isRequired,
};

export default ProductList;