import React from "react";

const {
    Provider: DatastoreServiceProvider,
    Consumer: DatastoreServiceConsumer
} = React.createContext();

export {
    DatastoreServiceProvider,
    DatastoreServiceConsumer
}