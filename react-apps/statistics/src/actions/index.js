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

const statLoaded = (data) => {
    return {
        type: 'FETCH_STAT_SUCCESS',
        payload: data
    }
}

const sendersLoaded = (data) => {
    return {
        type: 'FETCH_SENDERS_SUCCESS',
        payload: data
    }
}

const sendersRequested = () => {
    return {
        type: 'FETCH_SENDERS_REQUEST'
    }
}

const sendersError = (error) => {
    return {
        type: 'FETCH_SENDERS_FAILURE',
        payload: error
    }
}

const projectLoaded = (data) => {
    return {
        type: 'FETCH_PROJECT_SUCCESS',
        payload: data
    }
}

const projectRequested = () => {
    return {
        type: 'FETCH_PROJECT_REQUEST'
    }
}

const projectError = (error) => {
    return {
        type: 'FETCH_PROJECT_FAILURE',
        payload: error
    }
}

const usersLoaded = (data) => {
    return {
        type: 'FETCH_USERS_SUCCESS',
        payload: data
    }
}

const usersRequested = () => {
    return {
        type: 'FETCH_USERS_REQUEST'
    }
}

const usersError = (error) => {
    return {
        type: 'FETCH_USERS_FAILURE',
        payload: error
    }
}

const notificationsLoaded = (data) => {
    return {
        type: 'FETCH_NOTIFICATIONS_SUCCESS',
        payload: data
    }
}

const notificationRequested = () => {
    return {
        type: 'FETCH_NOTIFICATIONS_REQUEST'
    }
}

const notificationsError = (error) => {
    return {
        type: 'FETCH_NOTIFICATIONS_FAILURE',
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

const fetchProject = (datastoreService, dispatch) => {
    dispatch(projectRequested());
    datastoreService.fetchProject()
        .then((result) => result.json())
        .then((data) => {
            return dispatch(projectLoaded(data))})
        .catch((err) => dispatch(projectError(err)))
}

const fetchUsers = (datastoreService, dispatch) => {
    dispatch(usersRequested());
    datastoreService.fetchUsers()
        .then((result) => result.json())
        .then((data) => {
            return dispatch(usersLoaded(data))})
        .catch((err) => dispatch(usersError(err)))
}

const fetchLevelStat = (datastoreService, dispatch, level) => {
    datastoreService.fetchStat(level)
        .then((result) => result.json())
        .then((data) => {
            return dispatch(statLoaded(data))})
}

const fetchMessageStat = (datastoreService, dispatch, id) => {
    datastoreService.fetchMessageStat(id)
        .then((result) => result.json())
        .then((data) => {
            return dispatch(statLoaded(data))
        })
}

const fetchSenders = (datastoreService, dispatch) => {
    dispatch(sendersRequested());
    datastoreService.fetchSenders()
        .then((result) => result.json())
        .then((data) => {
            return dispatch(sendersLoaded(data))})
        .catch((err) => dispatch(sendersError(err)))
}

const fetchNotifications = (datastoreService, dispatch) => {
    dispatch(notificationRequested());
    datastoreService.fetchNotifications()
        .then((result) => result.json())
        .then((data) => {
            return dispatch(notificationsLoaded(data))})
        .catch((err) => dispatch(notificationsError(err)))
}

const fetchSaveNotification = (datastoreService, dispatch, data, reloadHandler) => {
    datastoreService.saveNotification(data)
        .then((result) => {
            if (data.id === undefined) {
                reloadHandler();
            }
        })
}

const fetchRemoveNotification = (datastoreService, dispatch, data, reloadHandler) => {
    datastoreService.removeNotification(data)
        .then((result) => {
                reloadHandler();
        })
}

const fetchRemoveUser = (datastoreService, dispatch, data, reloadHandler) => {
    datastoreService.removeUser(data)
        .then((result) => {
            reloadHandler();
        })
}

const fetchAddUser = (datastoreService, dispatch, email, reloadHandler) => {
    datastoreService.addUser(email)
        .then((result) => {
            reloadHandler();
        })
}

const fetchSetGmt = (datastoreService, dispatch, gmt, reloadHandler) => {
    datastoreService.setGmt(gmt)
        .then((result) => {
            reloadHandler();
        })
}

const fetchSetEnableServerCheck = (datastoreService, dispatch, enableServerCheck) => {
    datastoreService.setEnableServerCheck(enableServerCheck)
}

export {
    fetchMessages,
    fetchLevels,
    fetchProject,
    fetchUsers,
    fetchLevelStat,
    fetchMessageStat,
    fetchNotifications,
    fetchSenders,
    fetchSaveNotification,
    fetchRemoveNotification,
    fetchRemoveUser,
    fetchAddUser,
    fetchSetGmt,
    fetchSetEnableServerCheck
}