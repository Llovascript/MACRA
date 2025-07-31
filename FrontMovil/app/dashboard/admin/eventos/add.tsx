// AddEvento.tsx
"use client";

import { useState } from "react";
import {
  View,
  Text,
  StyleSheet,
  Image,
  TextInput,
  TouchableOpacity,
  ScrollView,
  Alert,
  ActivityIndicator,
  Platform,
} from "react-native";
import { MaterialCommunityIcons } from "@expo/vector-icons";
import { useRouter } from "expo-router";
import DateTimePicker from "@react-native-community/datetimepicker";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { generateFastApiUrl } from "../../../../utils";

import FondoImage from "../../../../assets/images/fondo.jpg";

export default function AddEvento() {
  const [formData, setFormData] = useState({
    nombre: "",
    fechaIn: new Date(),
    fechaTer: new Date(),   // coincide con el API
    descripcion: "",
    estatus_id: 1,
    del: 1, // siempre crear con del = 1
  });
  const [showPicker, setShowPicker] = useState<"inicio" | "termino" | null>(null);
  const [loading, setLoading] = useState(false);
  const router = useRouter();

  const updateField = (field: string, value: string | Date) => {
    setFormData(prev => ({ ...prev, [field]: value }));
  };

  const onDateChange = (_: any, selected?: Date) => {
    if (Platform.OS === "android") setShowPicker(null);
    if (!selected) return;
    updateField(showPicker === "inicio" ? "fechaIn" : "fechaTer", selected);
  };

  const submit = async () => {
    if (!formData.nombre.trim()) {
      Alert.alert("Error", "El nombre es obligatorio");
      return;
    }
    if (!formData.descripcion.trim()) {
      Alert.alert("Error", "La descripción es obligatoria");
      return;
    }
    if (formData.fechaTer <= formData.fechaIn) {
      Alert.alert("Error", "La fecha término debe ser posterior a la inicio");
      return;
    }

    setLoading(true);
    try {
      const token = await AsyncStorage.getItem("userToken");
      const url = generateFastApiUrl("/eventos/");

      const resp = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
          nombre: formData.nombre,
          fechaIn: formData.fechaIn.toISOString().split("T")[0],
          fechaTer: formData.fechaTer.toISOString().split("T")[0],
          descripcion: formData.descripcion,
          estatus_id: formData.estatus_id,
          del: 1,  // siempre crear con del = 1
        }),
      });

      if (resp.ok) {
        Alert.alert("Éxito", "Evento creado correctamente", [
          { text: "OK", onPress: () => router.back() },
        ]);
      } else {
        const err = await resp.json();
        const msg = Array.isArray(err.detail)
          ? err.detail.map((e: any) => `${e.loc.join(".")}: ${e.msg}`).join("\n")
          : err.detail || "No se pudo crear el evento";
        Alert.alert("Error", msg);
      }
    } catch {
      Alert.alert("Error", "No se pudo conectar con el servidor");
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <Image source={FondoImage} style={styles.bg} resizeMode="cover" />
      <ScrollView>
        <View style={styles.overlay}>
          <View style={styles.header}>
            <TouchableOpacity onPress={() => router.back()}>
              <MaterialCommunityIcons name="arrow-left" size={24} color="#333" />
            </TouchableOpacity>
            <Text style={styles.title}>Agregar evento</Text>
            <View style={{ width: 24 }} />
          </View>

          <View style={styles.form}>
            <TextInput
              style={styles.input}
              placeholder="Nombre"
              value={formData.nombre}
              onChangeText={v => updateField("nombre", v)}
            />

            <TouchableOpacity
              style={styles.input}
              onPress={() => setShowPicker("inicio")}
            >
              <Text>Inicio: {formData.fechaIn.toLocaleDateString()}</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={styles.input}
              onPress={() => setShowPicker("termino")}
            >
              <Text>Término: {formData.fechaTer.toLocaleDateString()}</Text>
            </TouchableOpacity>

            <TextInput
              style={[styles.input, styles.textArea]}
              placeholder="Descripción"
              value={formData.descripcion}
              onChangeText={v => updateField("descripcion", v)}
              multiline
              numberOfLines={4}
            />

            {showPicker && (
              <DateTimePicker
                value={showPicker === "inicio" ? formData.fechaIn : formData.fechaTer}
                mode="date"
                display={Platform.OS === "ios" ? "spinner" : "default"}
                onChange={onDateChange}
                minimumDate={new Date()}
              />
            )}

            <View style={styles.buttons}>
              <TouchableOpacity style={styles.btnReset} onPress={() => {
                setFormData({
                  nombre: "",
                  fechaIn: new Date(),
                  fechaTer: new Date(),
                  descripcion: "",
                  estatus_id:1,
                  del: 1,
                });
              }}>
                <Text style={styles.btnText}>Restablecer</Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={styles.btnSubmit}
                onPress={submit}
                disabled={loading}
              >
                {loading
                  ? <ActivityIndicator color="white" />
                  : <Text style={styles.btnText}>Agregar</Text>
                }
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  bg: { position: "absolute", width: "100%", height: "100%" },
  overlay: { backgroundColor: "rgba(255,255,255,0.9)", padding: 20, paddingTop: 60 },
  header: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", marginBottom: 30 },
  title: { fontSize: 24, fontWeight: "bold" },
  form: { gap: 15 },
  input: {
    backgroundColor: "#f5f5f5",
    borderRadius: 8,
    padding: 12,
  },
  textArea: { minHeight: 80 },
  buttons: { flexDirection: "row", justifyContent: "space-between", marginTop: 20 },
  btnReset: {
    flex: 1, marginRight: 10, backgroundColor: "#FF9800", padding: 12, borderRadius: 8, alignItems: "center"
  },
  btnSubmit: {
    flex: 1, marginLeft: 10, backgroundColor: "#4CAF50", padding: 12, borderRadius: 8, alignItems: "center"
  },
  btnText: { color: "white", fontWeight: "600" },
});
