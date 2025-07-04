import { Link } from 'react-router-dom';

const ProductCard = ({ product }) => (
    <Link
        to={`/products/${product.id}`}
        className="block border p-4 rounded shadow hover:shadow-lg transition"
    >
        <div className="relative w-full h-48 mb-2 rounded overflow-hidden">
            <img
                src={product.image_url || '/storage/products/fallback_image1.png'}
                alt={product.name}
                className="w-full h-full object-contain"
                onError={(e) => {
                    e.target.src = '/storage/products/fallback_image1.png';
                }}
            />
        </div>
        <h3 className="text-lg font-bold">{product.name}</h3>
        <p className="text-secondary">{product.article}</p>
        <p className="text-secondary">Manufacturer: {product.manufacturer_name}</p>
        <div className="text-lg">
            {product.prices && (
                <div>
                    {Object.entries(product.prices).map(([currency, price]) => (
                        <span key={currency} className="mr-2">
                            {currency}: {price.toLocaleString('en-US', { maximumFractionDigits: 2 })}
                        </span>
                    ))}
                </div>
            )}
        </div>
    </Link>
);

export default ProductCard;