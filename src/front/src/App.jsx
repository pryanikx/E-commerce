import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Navbar from './components/Navbar';
import Login from './components/Login';
import Register from './components/Register';
import Home from './components/Home';
import AdminPanel from './components/AdminPanel';
import ProductDetails from './components/ProductDetails';

const App = () => {
    const role = localStorage.getItem('role');
    const isAuthenticated = !!localStorage.getItem('token');

    return (
        <BrowserRouter>
            <Navbar />
            <Routes>
                <Route path="/login" element={<Login />} />
                <Route path="/register" element={<Register />} />
                <Route path="/" element={isAuthenticated ? <Home /> : <Login />} />
                <Route
                    path="/admin"
                    element={isAuthenticated && role === 'admin' ? <AdminPanel /> : <Login />}
                />
                <Route path="/products/:id" element={<ProductDetails />} />
            </Routes>
        </BrowserRouter>
    );
};

export default App;