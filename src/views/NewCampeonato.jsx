import {useNavigate} from "react-router-dom";
import {useEffect, useState} from "react";
import axiosClient from "../axios-client.js";
import {useStateContext} from "../context/ContextProvider.jsx";

export default function NewCampeonato() {
    const navigate = useNavigate();
    const [times, setTimes] = useState([]);
    const [campeonato, setCampeonato] = useState({
        id: null,
        nome: '',
        times: []
    })
    const [errors, setErrors] = useState(null)
    const [loading, setLoading] = useState(false);
    const {setNotification} = useStateContext()

    useEffect(() => {
        getTimes();
    }, [])

    const selectTime = (timeId) => {
        setCampeonato(prevSelected => {
            const newTimes = prevSelected.times.includes(timeId)
                ? prevSelected.times.filter(id => id !== timeId)
                : [...prevSelected.times, timeId];

            return { ...prevSelected, times: newTimes };
        });
    };

    const getTimes = () => {
        setLoading(true)
        axiosClient.get('/times')
            .then(({data}) => {
                setLoading(false)
                setTimes(data)
            })
            .catch(() => {
                setLoading(false)
            })
    }

    const onSubmit = ev => {
        ev.preventDefault();
        if (campeonato.times.length === 8) {
            axiosClient.post('/campeonatos', campeonato)
                .then(({data}) => {
                    setNotification('Campeonato was successfully created')
                    navigate('/campeonatos')
                })
                .catch(err => {
                    const response = err.response;
                    if (response && response.status === 422) {
                        setErrors(response.data.errors)
                    }
                })
        }
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
                {!loading && (
                    <form onSubmit={onSubmit}>
                        <div className="card-container">
                            <input value={campeonato.nome} onChange={ev => setCampeonato({...campeonato, nome: ev.target.value})}
                                   placeholder="Nome"/>
                            {times.map((t, index) => (
                                <div key={t.id}
                                     className={`card-item ${index % 8 < 4 ? 'first-row' : 'second-row'} ${campeonato.times.includes(t.id) ? 'selected' : ''}`}
                                     onClick={() => selectTime(t.id)}>
                                    <div className="card-content">
                                        <div className="card-title">{t.nome}</div>
                                    </div>
                                </div>
                            ))}
                        </div>
                        <button className="btn">Enviar</button>
                    </form>
                )}
            </div>

        </div>
    )
}
