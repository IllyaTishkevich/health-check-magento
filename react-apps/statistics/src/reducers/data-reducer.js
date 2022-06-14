const initialState = {
    messages: {
        data: {},
        loading: true,
        error: '',
    },
    levels: {
        data: [],
        loading: true,
        error: '',
    },
    stat: {
        data: {},
        loading: true,
        error: '',
    },
    notification: {
        data: {},
        loading: true,
        error: '',
    },
    senders: {
        data: {},
        loading: true,
        error: '',
    },
    project: {
        data: {},
        loading: true,
        error: '',
    },
    users: {
        data: {},
        loading: true,
        error: '',
    }
};

const updateData = (state, action) => {
    if (state === undefined) {
        return initialState;
    }

    switch (action.type) {
        case 'FETCH_MESSAGES_REQUEST':
            return {
                ...state,
                messages: {
                    data: {},
                    loading: true,
                    error: '',
                },
            };
        case 'FETCH_MESSAGES_SUCCESS':
            return {
                ...state,
                messages: {
                    data: action.payload,
                    loading: false,
                    error: '',
                }
            };
        case 'FETCH_MESSAGES_FAILURE':
            return {
                ...state,
                messages: {
                    data: {},
                    loading: false,
                    error: action.payload,
                }
            };
        case 'FETCH_USERS_REQUEST':
            return {
                ...state,
                users: {
                    data: {},
                    loading: true,
                    error: '',
                },
            };
        case 'FETCH_USERS_SUCCESS':
            return {
                ...state,
                users: {
                    data: action.payload,
                    loading: false,
                    error: '',
                }
            };
        case 'FETCH_USERS_FAILURE':
            return {
                ...state,
                users: {
                    data: {},
                    loading: false,
                    error: action.payload,
                }
            };
        case 'FETCH_PROJECT_REQUEST':
            return {
                ...state,
                project: {
                    data: {},
                    loading: true,
                    error: '',
                },
            };
        case 'FETCH_PROJECT_SUCCESS':
            return {
                ...state,
                project: {
                    data: action.payload,
                    loading: false,
                    error: '',
                }
            };
        case 'FETCH_PROJECT_FAILURE':
            return {
                ...state,
                project: {
                    data: {},
                    loading: false,
                    error: action.payload,
                }
            };
        case 'FETCH_NOTIFICATIONS_REQUEST':
            return {
                ...state,
                notification: {
                    data: {},
                    loading: true,
                    error: '',
                },
            };
        case 'FETCH_NOTIFICATIONS_SUCCESS':
            return {
                ...state,
                notification: {
                    data: action.payload,
                    loading: false,
                    error: '',
                }
            };
        case 'FETCH_NOTIFICATIONS_FAILURE':
            return {
                ...state,
                notification: {
                    data: {},
                    loading: false,
                    error: action.payload,
                }
            };
        case 'FETCH_LEVELS_REQUEST':
            return {
                ...state,
                levels: {
                    data: [],
                    loading: true,
                    error: '',
                },
            };
        case 'FETCH_LEVELS_SUCCESS':
            return {
                ...state,
                levels: {
                    data: action.payload,
                    loading: false,
                    error: '',
                }
            };
        case 'FETCH_LEVELS_FAILURE':
            return {
                ...state,
                levels: {
                    data: [],
                    loading: false,
                    error: action.payload,
                }
            };
        case 'FETCH_STAT_REQUEST':
            return {
                ...state,
                stat: {
                    data: state.stat.data,
                    loading: true,
                    error: ''
                }
            };
        case 'FETCH_STAT_FAILURE':
            Object.assign(state.stat.data, action.payload);
            return {
                ...state,
                stat: {
                    data: state.stat.data,
                    loading: false,
                    error: action.payload
                }
            };
        case 'FETCH_STAT_SUCCESS':
            Object.assign(state.stat.data, action.payload);
            return {
                ...state,
                stat: {
                    data: state.stat.data,
                    loading: false,
                    error: ''
                }
            };
        case 'FETCH_SENDERS_REQUEST':
            return {
                ...state,
                senders: {
                    data: {},
                    loading: true,
                    error: '',
                },
            };
        case 'FETCH_SENDERS_SUCCESS':
            return {
                ...state,
                senders: {
                    data: action.payload,
                    loading: false,
                    error: '',
                }
            };
        case 'FETCH_SENDERS_FAILURE':
            return {
                ...state,
                senders: {
                    data: {},
                    loading: false,
                    error: action.payload,
                }
            };
        default:
            return {...state};
    }
};

export default updateData;