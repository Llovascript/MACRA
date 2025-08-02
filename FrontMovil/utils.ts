import { Constants } from "expo-constants";

const FASTAPI_DEV_BASE_URL = 'http://192.168.100.4:5001';

export const generateFastApiUrl = (relativePath: string) => {
    const path = relativePath.startsWith('/') ? relativePath : `/${relativePath}`;

    if (process.env.NODE_ENV === 'development') {
        return FASTAPI_DEV_BASE_URL.concat(path);
    }

    if (!process.env.FASTAPI_BASE_URL) {
        throw new Error('FASTAPI_BASE_URL environment is not defined in production production');
    }
    return process.env.FASTAPI_BASE_URL.concat(path);
};