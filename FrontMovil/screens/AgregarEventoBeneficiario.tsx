import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  SafeAreaView,
  Alert,
} from 'react-native';
import Icon from 'react-native-vector-icons/MaterialIcons';

const AgregarEvento = ({ navigation }) => {
  const [formData, setFormData] = useState({
    nombreEvento: '',
    fechaInicio: '',
    fechaTermino: '',
    descripcion: '',
  });

  const handleInputChange = (field, value) => {
    setFormData(prev => ({
      ...prev,
      [field]: value
    }));
  };

  const handleReset = () => {
    setFormData({
      nombreEvento: '',
      fechaInicio: '',
      fechaTermino: '',
      descripcion: '',
    });
  };

  const handleAdd = () => {
    // Validación básica
    if (!formData.nombreEvento.trim()) {
      Alert.alert('Error', 'Por favor ingresa el nombre del evento');
      return;
    }

    // Lógica para agregar evento
    console.log('Agregando evento:', formData);
    
    Alert.alert(
      'Éxito',
      'Evento agregado correctamente',
      [
        {
          text: 'OK',
          onPress: () => {
            handleReset();
            // navigation.goBack(); // Opcional: regresar a la pantalla anterior
          }
        }
      ]
    );
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity 
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Icon name="arrow-back" size={24} color="#000" />
        </TouchableOpacity>
        <Text style={styles.title}>Agregar evento</Text>
      </View>

      <View style={styles.formContainer}>
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Información del Evento</Text>
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.inputLabel}>Texto</Text>
          <TextInput
            style={styles.textInput}
            value={formData.nombreEvento}
            onChangeText={(value) => handleInputChange('nombreEvento', value)}
            placeholder="Nombre del evento"
            placeholderTextColor="#999"
          />
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.inputLabel}>Texto</Text>
          <TextInput
            style={styles.textInput}
            value={formData.fechaInicio}
            onChangeText={(value) => handleInputChange('fechaInicio', value)}
            placeholder="Fecha de inicio"
            placeholderTextColor="#999"
          />
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.inputLabel}>Texto</Text>
          <TextInput
            style={styles.textInput}
            value={formData.fechaTermino}
            onChangeText={(value) => handleInputChange('fechaTermino', value)}
            placeholder="Fecha de término"
            placeholderTextColor="#999"
          />
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.inputLabel}>Texto</Text>
          <TextInput
            style={styles.textInput}
            value={formData.descripcion}
            onChangeText={(value) => handleInputChange('descripcion', value)}
            placeholder="Descripción del evento"
            placeholderTextColor="#999"
            multiline={true}
            numberOfLines={3}
          />
        </View>

        <View style={styles.buttonContainer}>
          <TouchableOpacity style={styles.resetButton} onPress={handleReset}>
            <Text style={styles.resetButtonText}>Restablecer</Text>
          </TouchableOpacity>
          
          <TouchableOpacity style={styles.addButton} onPress={handleAdd}>
            <Text style={styles.addButtonText}>Agregar</Text>
          </TouchableOpacity>
        </View>
      </View>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 12,
    backgroundColor: '#fff',
  },
  backButton: {
    marginRight: 16,
  },
  title: {
    fontSize: 18,
    fontWeight: '500',
    color: '#000',
  },
  formContainer: {
    flex: 1,
    margin: 16,
    backgroundColor: '#fff',
    borderRadius: 8,
    padding: 16,
  },
  sectionHeader: {
    backgroundColor: '#e0e0e0',
    padding: 12,
    marginBottom: 16,
    borderRadius: 4,
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: '500',
    color: '#333',
  },
  inputGroup: {
    marginBottom: 16,
  },
  inputLabel: {
    fontSize: 12,
    color: '#666',
    marginBottom: 4,
  },
  textInput: {
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 4,
    padding: 12,
    fontSize: 14,
    backgroundColor: '#fff',
    textAlignVertical: 'top', // Para multiline
  },
  buttonContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: 32,
  },
  resetButton: {
    backgroundColor: '#ff9800',
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 20,
    flex: 0.45,
  },
  resetButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '500',
    textAlign: 'center',
  },
  addButton: {
    backgroundColor: '#4caf50',
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 20,
    flex: 0.45,
  },
  addButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '500',
    textAlign: 'center',
  },
});

export default AgregarEventoBeneficiario;
