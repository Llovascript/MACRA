import AsyncStorage from "@react-native-async-storage/async-storage";
import BeneficiarioStack from "./BeneficiarioStack";
import DonadorStack from "./DonadorStack";
import React, { useEffect, useState } from "react";

export default function AppNavigator() {
const [rol, setRol] = useState<string | null>(null);

useEffect(() => {
    const fetchRol = async () => {
    const storedRol = await AsyncStorage.getItem("rol");
    setRol(storedRol);
    };
    fetchRol();
    }, []);

  if (!rol) return null; // Loader temporal

    return rol === "beneficiario" ? <BeneficiarioStack /> : <DonadorStack />;
}
