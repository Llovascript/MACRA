import Constants from "expo-constants";

const { FASTAPI_BASE_URL, FASTAPI_DEV_BASE_URL } = Constants.expoConfig?.extra || {};

export const generateFastApiUrl = (relativePath: string) => {
  const path = relativePath.startsWith('/') ? relativePath : `/${relativePath}`;

  if (__DEV__) {
    if (!FASTAPI_DEV_BASE_URL) {
      throw new Error('FASTAPI_DEV_BASE_URL no está definido en desarrollo');
    }
    return FASTAPI_DEV_BASE_URL.concat(path);
  }

  if (!FASTAPI_BASE_URL) {
    throw new Error('FASTAPI_BASE_URL no está definido en producción');
  }

  return FASTAPI_BASE_URL.concat(path);
};
