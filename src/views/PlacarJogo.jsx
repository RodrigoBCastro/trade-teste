import {Link, useNavigate, useParams} from "react-router-dom";
import {useEffect, useState} from "react";
import axiosClient from "../axios-client.js";
import {useStateContext} from "../context/ContextProvider.jsx";

export default function PlacarJogo() {
    const navigate = useNavigate();
    let {id} = useParams();
    const [jogo, setJogo] = useState([]);
    const [golsCasa, setGolsCasa] = useState(0);
    const [golsVisitante, setGolsVisitante] = useState(0);
    const [errors, setErrors] = useState(null)
    const [loading, setLoading] = useState(false);
    const {setNotification} = useStateContext()

    if (id) {
        useEffect(() => {
            setLoading(true)
            axiosClient.get(`/jogos/${id}`)
                .then(({data}) => {
                    setLoading(false)
                    setJogo(data);
                    setGolsCasa(data.gols_time_casa);
                    setGolsVisitante(data.gols_time_visitante);
                })
                .catch(() => {
                    setLoading(false)
                })
        }, [])
    }

    const onSubmit = (event) => {
        event.preventDefault();

        const placar = {
            gols_time_casa: parseInt(golsCasa, 10),
            gols_time_visitante: parseInt(golsVisitante, 10)
        };

        axiosClient.put(`/jogos/${id}/resultado`, placar)
            .then(() => {
                setNotification('Placar Atualizado')
                navigate(`/campeonatos/${jogo.campeonato_id}`)
            })
            .catch(err => {
                const response = err.response;
                if (response && response.status === 422) {
                    setErrors(response.data.errors)
                }
            })
    };

    return (
        <div>
            <div className="card animated fadeInDown">
                {loading && (
                    <div className="loading">Loading...</div>
                )}
                {errors &&
                    <div className="alert">
                        {Object.keys(errors).map(key => (
                            <p key={key}>{errors[key][0]}</p>
                        ))}
                    </div>
                }
                {!loading && jogo && (
                    <form onSubmit={onSubmit}>
                        <h2>{jogo.time_casa && jogo.time_casa.nome} VS {jogo.time_visitante && jogo.time_visitante.nome}</h2>
                        <div>
                            <label htmlFor="golsCasa">{jogo.time_casa && jogo.time_casa.nome}</label>
                            <input
                                type="number"
                                id="golsCasa"
                                value={golsCasa}
                                onChange={e => setGolsCasa(e.target.value)}
                                min="0"
                            />
                        </div>
                        <div>
                            <label htmlFor="golsVisitante">{jogo.time_visitante && jogo.time_visitante.nome}</label>
                            <input
                                type="number"
                                id="golsVisitante"
                                value={golsVisitante}
                                onChange={e => setGolsVisitante(e.target.value)}
                                min="0"
                            />
                        </div>
                        <button className="btn">Salvar</button>
                    </form>
                )}
            </div>
        </div>
    )
}
