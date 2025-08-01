import AsyncStorage from "@react-native-async-storage/async-storage";

const API_URL = "https://api.example.com/"; //Aqui se colocara el URL real de la API

const getToken = async () => {
    return await AsyncStorage.getItem("token");
};

const request = async (endpoint: string, method: string = "GET", body?: any) => {
const token = await getToken();
const headers: Record<string, string> = {
    "Content-Type": "application/json",
};
    if (token) headers.Authorization = `Bearer ${token}`;

const response = await fetch(`${API_URL}${endpoint}`, {
    method,
    headers,
    body: body ? JSON.stringify(body) : undefined,
});

if (!response.ok) {
    const errorData = await response.json();
    throw new Error(errorData.detail || "Error en la API");
}

return response.json();
    };

export const api = {
    get: (endpoint: string) => request(endpoint, "GET"),
    post: (endpoint: string, data: any) => request(endpoint, "POST", data),
    patch: (endpoint: string, data: any) => request(endpoint, "PATCH", data),
    delete: (endpoint: string) => request(endpoint, "DELETE"),
};
