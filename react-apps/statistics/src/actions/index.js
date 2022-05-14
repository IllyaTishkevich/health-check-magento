const messagesLoaded = (data) => {
    return {
        type: 'FETCH_MESSAGES_SUCCESS',
        payload: data
    }
}

const messagesRequested = () => {
    return {
        type: 'FETCH_MESSAGES_REQUEST'
    }
}

const messagesError = (error) => {
    return {
        type: 'FETCH_MESSAGES_FAILURE',
        payload: error
    }
}

const levelsLoaded = (data) => {
    return {
        type: 'FETCH_LEVELS_SUCCESS',
        payload: data
    }
}

const levelsRequested = () => {
    return {
        type: 'FETCH_LEVELS_REQUEST'
    }
}

const levelsError = (error) => {
    return {
        type: 'FETCH_LEVELS_FAILURE',
        payload: error
    }
}

const fetchMessages = (datastoreService, dispatch) => {
    dispatch(messagesRequested());
    datastoreService.fetchMessages()
        .then((result) => result.json())
        .then((data) => {
            return dispatch(messagesLoaded(data))})
        .catch((err) => dispatch(messagesError(err)))
}

const fetchLevels = (datastoreService, dispatch) => {
    dispatch(levelsRequested());
    datastoreService.fetchLevels()
        .then((result) => result.json())
        .then((data) => {
            return dispatch(levelsLoaded(data))})
        .catch((err) => dispatch(levelsError(err)))
}

export {
    fetchMessages,
    fetchLevels
}