const initialState = {
    messages: {
        data: [],
        loading: true,
        error: null,
    },
    levels: {
        data: [],
        loading: true,
        error: null,
    },
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
                    data: [],
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
                    data: [],
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
        default:
            return {...state};
    }
}

export default updateData;