"use client";

import { useState, useEffect } from "react";
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
  FlatList,
  Platform,
} from "react-native";
import { MaterialCommunityIcons } from "@expo/vector-icons";
import { useRouter } from "expo-router";
import DateTimePicker from "@react-native-community/datetimepicker";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { generateFastApiUrl } from "../../../../utils";

import FondoImage from "../../../../assets/images/fondo.jpg";

interface Evento {
  id: number;
  nombre: string;
  fechaIn: string;
  fechaTer: string;       // renombrado
  descripcion: string;
  estatus_id: number;
}

export default function UpdateEvento() {
  const [eventos, setEventos] = useState<Evento[]>([]);
  const [selectedEvento, setSelectedEvento] = useState<Evento | null>(null);
  const [formData, setFormData] = useState({
    nombre: "",
    fechaIn: new Date(),
    fechaTer: new Date(),   // renombrado
    descripcion: "",
    estatus_id: 1,
  });
  const [showDatePicker, setShowDatePicker] = useState<"inicio" | "termino" | null>(null);
  const [loading, setLoading] = useState(false);
  const [loadingEventos, setLoadingEventos] = useState(true);
  const router = useRouter();

  useEffect(() => {
    loadEventos();
  }, []);

  const loadEventos = async () => {
    try {
      setLoadingEventos(true);
      const token = await AsyncStorage.getItem("userToken");
      const apiUrl = generateFastApiUrl("/eventos/");

      const resp = await fetch(apiUrl, {
        headers: { Authorization: `Bearer ${token}` },
      });

      if (resp.ok) {
        const data = await resp.json();
        setEventos(data);
      } else {
        Alert.alert("Error", "No se pudieron cargar los eventos");
      }
    } catch (err) {
      console.error("Error loading eventos:", err);
      Alert.alert("Error", "No se pudo conectar con el servidor");
    } finally {
      setLoadingEventos(false);
    }
  };

  const selectEvento = (evt: Evento) => {
    setSelectedEvento(evt);
    setFormData({
      nombre: evt.nombre,
      fechaIn: new Date(evt.fechaIn),
      fechaTer: new Date(evt.fechaTer),
      descripcion: evt.descripcion,
      estatus_id: evt.estatus_id,
    });
  };

  const updateFormData = (field: string, value: string | Date | number) => {
    setFormData((p) => ({ ...p, [field]: value }));
  };

  const handleDateChange = (_: any, selectedDate?: Date) => {
    if (Platform.OS === "android") setShowDatePicker(null);
    if (!selectedDate) return;
    if (showDatePicker === "inicio") {
      updateFormData("fechaIn", selectedDate);
    } else if (showDatePicker === "termino") {
      updateFormData("fechaTer", selectedDate);
    }
  };

  const handleReset = () => {
    if (!selectedEvento) return;
    setFormData({
      nombre: selectedEvento.nombre,
      fechaIn: new Date(selectedEvento.fechaIn),
      fechaTer: new Date(selectedEvento.fechaTer),
      descripcion: selectedEvento.descripcion,
      estatus_id: selectedEvento.estatus_id,
    });
  };

  const handleSubmit = async () => {
    if (!selectedEvento) {
      Alert.alert("Error", "Selecciona un evento para actualizar");
      return;
    }
    if (!formData.nombre.trim()) {
      Alert.alert("Error", "El nombre del evento es obligatorio");
      return;
    }
    if (!formData.descripcion.trim()) {
      Alert.alert("Error", "La descripción del evento es obligatoria");
      return;
    }
    if (formData.fechaTer <= formData.fechaIn) {
      Alert.alert("Error", "La fecha de término debe ser posterior a la fecha de inicio");
      return;
    }

    setLoading(true);
    try {
      const token = await AsyncStorage.getItem("userToken");
      const apiUrl = generateFastApiUrl(`/eventos/${selectedEvento.id}`);

      const resp = await fetch(apiUrl, {
        method: "PUT",
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
        }),
      });

      if (resp.ok) {
        Alert.alert("Éxito", "Evento actualizado correctamente", [
          { text: "OK", onPress: () => router.back() },
        ]);
      } else {
        const errorData = await resp.json();
        let msg = "No se pudo actualizar el evento";

        if (Array.isArray(errorData.detail)) {
          msg = errorData.detail
            .map((e: any) => `${e.loc?.join(".")}: ${e.msg}`)
            .join("\n");
        } else if (typeof errorData.detail === "string") {
          msg = errorData.detail;
        }

        Alert.alert("Error", msg);
      }
    } catch (err) {
      console.error("Error updating evento:", err);
      Alert.alert("Error", "No se pudo conectar con el servidor");
    } finally {
      setLoading(false);
    }
  };

  if (!selectedEvento) {
    return (
      <View style={styles.containerWithBackground}>
        <Image source={FondoImage} style={styles.backgroundImage} resizeMode="cover" />
        <View style={styles.overlay}>
          <View style={styles.header}>
            <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
              <MaterialCommunityIcons name="arrow-left" size={24} color="#333" />
            </TouchableOpacity>
            <Text style={styles.title}>Actualizar evento</Text>
            <View style={{ width: 40 }} />
          </View>

          <View style={styles.listContainer}>
            <Text style={styles.subtitle}>Selecciona un evento para actualizar:</Text>
            {loadingEventos ? (
              <ActivityIndicator size="large" color="#8B4513" style={{ marginTop: 50 }} />
            ) : (
              <FlatList
                data={eventos}
                keyExtractor={(item) => item.id.toString()}
                renderItem={({ item }) => (
                  <TouchableOpacity style={styles.eventoItem} onPress={() => selectEvento(item)}>
                    <Text style={styles.eventoNombre}>{item.nombre}</Text>
                    <Text style={styles.eventoFecha}>
                      {new Date(item.fechaIn).toLocaleDateString()} —{" "}
                      {new Date(item.fechaTer).toLocaleDateString()}
                    </Text>
                  </TouchableOpacity>
                )}
                showsVerticalScrollIndicator={false}
              />
            )}
          </View>
        </View>
      </View>
    );
  }

  return (
    <View style={styles.containerWithBackground}>
      <Image source={FondoImage} style={styles.backgroundImage} resizeMode="cover" />
      <ScrollView style={styles.scrollContainer}>
        <View style={styles.overlay}>
          <View style={styles.header}>
            <TouchableOpacity style={styles.backButton} onPress={() => setSelectedEvento(null)}>
              <MaterialCommunityIcons name="arrow-left" size={24} color="#333" />
            </TouchableOpacity>
            <Text style={styles.title}>Actualizar evento</Text>
            <View style={{ width: 40 }} />
          </View>

          <View style={styles.formContainer}>
            <Text style={styles.sectionTitle}>Información del Evento</Text>
            <View style={styles.form}>
              {/* Nombre */}
              <View style={styles.inputContainer}>
                <TextInput
                  style={styles.input}
                  placeholder="Nombre del evento"
                  value={formData.nombre}
                  onChangeText={(v) => updateFormData("nombre", v)}
                />
              </View>

              {/* Fecha inicio */}
              <TouchableOpacity
                style={styles.inputContainer}
                onPress={() => setShowDatePicker("inicio")}
              >
                <Text style={styles.dateText}>
                  Fecha de inicio: {formData.fechaIn.toLocaleDateString()}
                </Text>
              </TouchableOpacity>

              {/* Fecha término */}
              <TouchableOpacity
                style={styles.inputContainer}
                onPress={() => setShowDatePicker("termino")}
              >
                <Text style={styles.dateText}>
                  Fecha de término: {formData.fechaTer.toLocaleDateString()}
                </Text>
              </TouchableOpacity>

              {/* Descripción */}
              <View style={[styles.inputContainer, styles.textAreaContainer]}>
                <TextInput
                  style={[styles.input, styles.textArea]}
                  placeholder="Descripción del evento"
                  value={formData.descripcion}
                  onChangeText={(v) => updateFormData("descripcion", v)}
                  multiline
                  numberOfLines={4}
                  textAlignVertical="top"
                />
              </View>

              <View style={styles.buttonContainer}>
                <TouchableOpacity style={styles.resetButton} onPress={handleReset}>
                  <Text style={styles.resetButtonText}>Restablecer</Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={styles.submitButton}
                  onPress={handleSubmit}
                  disabled={loading}
                >
                  {loading ? (
                    <ActivityIndicator color="white" />
                  ) : (
                    <Text style={styles.submitButtonText}>Actualizar</Text>
                  )}
                </TouchableOpacity>
              </View>
            </View>
          </View>

          {showDatePicker && (
            <DateTimePicker
              value={showDatePicker === "inicio" ? formData.fechaIn : formData.fechaTer}
              mode="date"
              display={Platform.OS === "ios" ? "spinner" : "default"}
              onChange={handleDateChange}
              minimumDate={new Date()}
            />
          )}
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  containerWithBackground: { flex: 1 },
  backgroundImage: {
    position: "absolute",
    top: 0,
    left: 0,
    bottom: 0,
    right: 0,
    width: "100%",
    height: "100%",
  },
  scrollContainer: { flex: 1 },
  overlay: {
    backgroundColor: "rgba(255,255,255,0.9)",
    paddingTop: 60,
    paddingBottom: 40,
    minHeight: "100%",
  },
  header: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    paddingHorizontal: 20,
    marginBottom: 30,
  },
  backButton: {
    backgroundColor: "white",
    borderRadius: 20,
    padding: 8,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  title: { fontSize: 24, fontWeight: "bold", color: "#333" },
  listContainer: { flex: 1, paddingHorizontal: 20 },
  subtitle: {
    fontSize: 18,
    fontWeight: "600",
    color: "#333",
    marginBottom: 20,
    textAlign: "center",
  },
  eventoItem: {
    backgroundColor: "white",
    borderRadius: 10,
    padding: 15,
    marginBottom: 10,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
    elevation: 2,
  },
  eventoNombre: { fontSize: 16, fontWeight: "600", color: "#333", marginBottom: 5 },
  eventoFecha: { fontSize: 14, color: "#666" },
  formContainer: {
    backgroundColor: "white",
    margin: 20,
    borderRadius: 15,
    padding: 20,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  sectionTitle: { fontSize: 18, fontWeight: "600", color: "#333", marginBottom: 20, textAlign: "center" },
  form: { gap: 15 },
  inputContainer: {
    backgroundColor: "#f5f5f5",
    borderRadius: 8,
    paddingHorizontal: 15,
    paddingVertical: 12,
    minHeight: 50,
    justifyContent: "center",
  },
  textAreaContainer: { paddingVertical: 15, minHeight: 100 },
  input: { fontSize: 16, color: "#333" },
  textArea: { minHeight: 80, textAlignVertical: "top" },
  dateText: { fontSize: 16, color: "#333" },
  buttonContainer: { flexDirection: "row", justifyContent: "space-between", marginTop: 20, gap: 15 },
  resetButton: {
    backgroundColor: "#FF9800",
    paddingVertical: 12,
    paddingHorizontal: 30,
    borderRadius: 25,
    flex: 1,
    alignItems: "center",
  },
  resetButtonText: { color: "white", fontSize: 16, fontWeight: "600" },
  submitButton: {
    backgroundColor: "#2196F3",
    paddingVertical: 12,
    paddingHorizontal: 30,
    borderRadius: 25,
    flex: 1,
    alignItems: "center",
  },
  submitButtonText: { color: "white", fontSize: 16, fontWeight: "600" },
});
