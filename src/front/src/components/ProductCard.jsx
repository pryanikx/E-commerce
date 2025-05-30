import { Link } from 'react-router-dom';

const ProductCard = ({ product }) => (
    <Link to={`/products/${product.id}`} className="block border p-4 rounded shadow hover:shadow-lg transition">
        <img src={product.image_url} alt={product.name} className="w-full h-48 object-cover mb-2" />
        <h3 className="text-lg font-bold">{product.name}</h3>
        <p className="text-secondary">{product.article}</p>
        <p className="text-secondary">Manufacturer: {product.manufacturer_name}</p>
        <p className="text-lg font-semibold">${product.price}</p>
    </Link>
);

export default ProductCard;