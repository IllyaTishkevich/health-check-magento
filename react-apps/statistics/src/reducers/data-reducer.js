const initialState = {
    messages: {
        data: {},
        loading: true,
        error: null,
    },
    levels: {
        data: [],
        loading: true,
        error: null,
    },
    stat: {
        data: {},
        loading: true,
        error: null,
    }
}

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
                    error: null,
                },
            };
        case 'FETCH_MESSAGES_SUCCESS':
            return {
                ...state,
                messages: {
                    data: action.payload,
                    loading: false,
                    error: null,
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
        case 'FETCH_LEVELS_REQUEST':
            return {
                ...state,
                levels: {
                    data: [],
                    loading: true,
                    error: null,
                },
            };
        case 'FETCH_LEVELS_SUCCESS':
            return {
                ...state,
                levels: {
                    data: action.payload,
                    loading: false,
                    error: null,
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
                    error: null
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
                    error: null
                }
            };
        default:
            return {...state};
    }
}

export default updateData;