import {createBrowserRouter, Navigate} from "react-router-dom";
import Home from "./views/Home.jsx";
import DefaultLayout from "./components/DefaultLayout";
import GuestLayout from "./components/GuestLayout";
import Login from "./views/Login";
import NotFound from "./views/NotFound";
import Signup from "./views/Signup";
import Campeonatos from "./views/Campeonatos.jsx";
import NewCampeonato from "./views/NewCampeonato.jsx";
import VisualizarCampeonato from "./views/VisualizarCampeonato.jsx";
import PlacarJogo from "./views/PlacarJogo.jsx";

const router = createBrowserRouter([
    {
        path: '/',
        element: <DefaultLayout/>,
        children: [
            {
                path: '/',
                element: <Navigate to="/home"/>
            },
            {
                path: '/home',
                element: <Home/>
            },
            {
                path: '/campeonatos',
                element: <Campeonatos/>
            },
            {
                path: '/campeonatos/new',
                element: <NewCampeonato/>
            },
            {
                path: '/campeonatos/:id',
                element: <VisualizarCampeonato/>
            },
            {
                path: '/jogos/:id',
                element: <PlacarJogo/>
            },
        ]
    },
    {
        path: '/',
        element: <GuestLayout/>,
        children: [
            {
                path: '/login',
                element: <Login/>
            },
            {
                path: '/signup',
                element: <Signup/>
            }
        ]
    },
    {
        path: "*",
        element: <NotFound/>
    }
])

export default router;
