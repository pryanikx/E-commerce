import ProductCard from './ProductCard';

const CategoryCard = ({ category, products }) => {
    const productList = Array.isArray(products) ? products : [];

    return (
        <div className="mb-8">
            <h2 className="text-2xl font-bold mb-4">{category.name}</h2>
            {productList.length === 0 ? (
                <p className="text-secondary">No products available</p>
            ) : (
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    {productList.map((product) => (
                        <ProductCard key={product.id} product={product} />
                    ))}
                </div>
            )}
        </div>
    );
};

export default CategoryCard;