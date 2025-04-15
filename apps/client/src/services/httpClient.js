import axios from "axios";

const http = axios.create({
    baseURL: '/api',
    withCredentials: true,
});

const getRequest = async (endpoint, params = {}) => {
    try {
        const response = await http.get(endpoint, {
            params,
            headers: {
                'Accept': 'application/json'
            }
        });
        return response.data;
    } catch (error) {
        throw error.response?.data || error.message;
    }
};

// In httpClient.js or wherever postRequest is defined
const postRequest = async (endpoint, formData) => {
    try {

        const response = await http.post(endpoint, formData);
        console.log(response)
        return response.data;
    } catch (error) {

        throw error.response?.data || error.message;

    }
};



const deleteRequest = async (endpoint, id) => {
    try {
        const response = await http.delete(`${endpoint}/${id}`);
        console.log(response);
        return response.data;
    } catch (error) {
        throw error.response?.data || error.message;
    }
};
const patchRequest = async (endpoint, formData) => {
    try {
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
        const response = await http.patch(endpoint, formData);
        console.log("log of Patch request ", response);
        return response.data;
    } catch (error) {
        const errorDetails = {

            message: error.message,
            url: error.config?.url,
            status: error.response?.status,
            data: error.response?.data,
        };
        console.error('API Error:', errorDetails);
        throw error.response?.data || errorDetails.message;
    }
};


const patchOrderRequest = async (endpoint, payload) => {
    try {
        let headers = {};
        let dataToSend = payload;

        // Detect if payload is FormData
        if (payload instanceof FormData) {
            for (let [key, value] of payload.entries()) {
                console.log(key, value);
            }
        } else {
            headers['Content-Type'] = 'application/json';
            dataToSend = JSON.stringify(payload);
        }

        const response = await http.patch(endpoint, dataToSend, { headers });
        console.log("log of Patch request ", response);
        return response.data;
    } catch (error) {
        const errorDetails = {
            message: error.message,
            url: error.config?.url,
            status: error.response?.status,
            data: error.response?.data,
        };
        console.error('API Error:', errorDetails);
        throw error.response?.data || errorDetails.message;
    }
};



export { getRequest, postRequest, deleteRequest, patchRequest, patchOrderRequest };
