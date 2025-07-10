import { Link } from 'react-router-dom';
import { FALLBACK_IMAGE_URL } from '../constants';

const ProductCard = ({ product }) => (
    <Link
        to={`/products/${product.id}`}
        className="block border p-4 rounded shadow hover:shadow-lg transition"
    >
        <div className="relative w-full h-48 mb-2 rounded overflow-hidden">
            <img
                src={product.image_url ?? FALLBACK_IMAGE_URL}
                alt={product.name}
                className="w-full h-full object-contain"
                onError={(e) => {
                    e.currentTarget.src = FALLBACK_IMAGE_URL;
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