import {useEffect, useState} from "react";
import axiosClient from "../axios-client.js";
import {Link} from "react-router-dom";
import {useStateContext} from "../context/ContextProvider.jsx";

export default function Campeonatos() {
    const [campeonatos, setCampeonatos] = useState([]);
    const [loading, setLoading] = useState(false);
    const {setNotification} = useStateContext()

    useEffect(() => {
        getCampeonatos();
    }, [])

    const getCampeonatos = () => {
        setLoading(true)
        axiosClient.get('/campeonatos')
            .then(({data}) => {
                setLoading(false)
                console.log(data);
                setCampeonatos(data)
            })
            .catch(() => {
                setLoading(false)
            })
    }

    return (
        <div>
            <div style={{display: 'flex', justifyContent: "space-between", alignItems: "center"}}>
                <h1>Campeonatos</h1>
                <Link className="btn-add" to="/campeonatos/new">Add new</Link>
            </div>
            <div className="card animated fadeInDown">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Data Inicio</th>
                        <th>Data Fim</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    {loading &&
                        <tbody>
                        <tr>
                            <td colSpan="5" class="text-center">
                                Loading...
                            </td>
                        </tr>
                        </tbody>
                    }
                    {!loading &&
                        <tbody>
                        {campeonatos.map(c => (
                            <tr key={c.id}>
                                <td>{c.id}</td>
                                <td>{c.nome}</td>
                                <td>{c.data_inicio}</td>
                                <td>{c.data_fim}</td>
                                <td>
                                    <Link className="btn-edit" to={'/campeonatos/' + c.id}>Visualizar</Link>
                                </td>
                            </tr>
                        ))}
                        </tbody>
                    }
                </table>
            </div>
        </div>
    )
}
