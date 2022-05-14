import React from "react";
import { DatastoreServiceConsumer } from "../store-service-context";

const withStoreService = () => (Wrapped) => {
    return (props) => {
        return (
            <DatastoreServiceConsumer>
                {
                    (datastoreService) =>
                        <Wrapped {...props} datastoreService={datastoreService} />
                }
            </DatastoreServiceConsumer>
        )
    }
}

export default withStoreService;