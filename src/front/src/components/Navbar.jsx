import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import api from '../api/api';

const Navbar = () => {
    const [role, setRole] = useState(localStorage.getItem('role'));
    const [isAuthenticated, setIsAuthenticated] = useState(!!localStorage.getItem('token'));
    const navigate = useNavigate();

    // Отслеживаем изменения в localStorage
    useEffect(() => {
        const handleStorageChange = () => {
            setRole(localStorage.getItem('role'));
            setIsAuthenticated(!!localStorage.getItem('token'));
        };

        window.addEventListener('storage', handleStorageChange);
        // Проверяем при монтировании
        handleStorageChange();

        return () => {
            window.removeEventListener('storage', handleStorageChange);
        };
    }, []);

    const handleLogout = async () => {
        try {
            await api.post('/logout');
            localStorage.removeItem('token');
            localStorage.removeItem('role');
            setRole(null);
            setIsAuthenticated(false);
            navigate('/login');
        } catch (error) {
            console.error('Logout failed:', error);
        }
    };

    return (
        <nav className="bg-primary text-white p-4">
            <div className="container mx-auto flex justify-between items-center">
                <Link to="/" className="text-2xl font-bold">Shop</Link>
                <div>
                    {isAuthenticated ? (
                        <>
                            {role === 'admin' && (
                                <Link to="/admin" className="mr-4 hover:underline">Admin Panel</Link>
                            )}
                            <button onClick={handleLogout} className="hover:underline">Logout</button>
                        </>
                    ) : (
                        <>
                            <Link to="/login" className="mr-4 hover:underline">Login</Link>
                            <Link to="/register" className="hover:underline">Register</Link>
                        </>
                    )}
                </div>
            </div>
        </nav>
    );
};

export default Navbar;