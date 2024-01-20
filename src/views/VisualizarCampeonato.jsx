import {Link, useNavigate, useParams} from "react-router-dom";
import {useEffect, useState} from "react";
import axiosClient from "../axios-client.js";
import {useStateContext} from "../context/ContextProvider.jsx";

export default function VisualizarCampeonato() {
    const navigate = useNavigate();
    let {id} = useParams();
    const [campeonato, setCampeonato] = useState([]);
    const [errors, setErrors] = useState(null)
    const [loading, setLoading] = useState(false);
    const {setNotification} = useStateContext()

    if (id) {
        useEffect(() => {
            setLoading(true)
            axiosClient.get(`/campeonatos/${id}`)
                .then(({data}) => {
                    setLoading(false)
                    console.log(data);
                    setCampeonato(data);
                })
                .catch(() => {
                    setLoading(false)
                })
        }, [])
    }

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
                {!loading && (
                    <div className="visualizar-campeonato">
                        <h1 className="titulo-campeonato">{campeonato.nome}</h1>
                        <div className="jogos-container">
                            {campeonato.jogos && campeonato.jogos.map(jogo => (
                                <div key={jogo.id}
                                     className={`jogo-card ${jogo.resultado ? 'com-resultado' : 'sem-resultado'}`}>
                                    <div className="jogo-info">
                                        <span className="time">{jogo.time_casa.nome}</span>
                                        <span className="versus">VS</span>
                                        <span className="time">{jogo.time_visitante.nome}</span>
                                    </div>
                                    <div className="fase-jogo">
                                        Fase: {jogo.fase}
                                    </div>
                                    <div className="jogo-detalhes">
                                        {jogo.resultado ? (
                                            <div className="resultado">
                                                <span>{jogo.gols_time_casa} - {jogo.gols_time_visitante}</span>
                                                <span>Vencedor: {jogo.resultado.vencedor.nome}</span>
                                            </div>
                                        ) : (
                                            <div className="pendente">
                                                Jogo ainda não ocorreu
                                            </div>
                                        )}
                                        <div className="data-jogo">
                                            Data: {new Date(jogo.data_jogo).toLocaleString()}
                                        </div>
                                    </div>

                                    {!jogo.resultado && (
                                        <div className="placar-btn-container">
                                            <Link
                                                to={'/jogos/' + jogo.id}
                                                className="adicionar-placar-btn">
                                                ⚽
                                            </Link>
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </div>
    )
}
