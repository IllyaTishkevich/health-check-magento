import updateData from "./data-reducer";


const reducer = (state, action) => {
    return {
        ...updateData(state, action)
    }
}

export default reducer;