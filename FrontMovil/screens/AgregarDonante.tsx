import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  SafeAreaView,
} from 'react-native';
import Icon from 'react-native-vector-icons/MaterialIcons';

const AgregarDonante = ({ navigation }) => {
  const [formData, setFormData] = useState({
    campo1: '',
    campo2: '',
    campo3: '',
    campo4: '',
  });

  const handleInputChange = (field, value) => {
    setFormData(prev => ({
      ...prev,
      [field]: value
    }));
  };

  const handleReset = () => {
    setFormData({
      campo1: '',
      campo2: '',
      campo3: '',
      campo4: '',
    });
  };

  const handleAdd = () => {
    // Lógica para agregar donante
    console.log('Agregando donante:', formData);
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
        <Text style={styles.title}>Agregar donante</Text>
      </View>

      <View style={styles.formContainer}>
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Información del Donante</Text>
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.inputLabel}>Texto</Text>
          <TextInput
            style={styles.textInput}
            value={formData.campo1}
            onChangeText={(value) => handleInputChange('campo1', value)}
            placeholder=""
          />
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.inputLabel}>Texto</Text>
          <TextInput
            style={styles.textInput}
            value={formData.campo2}
            onChangeText={(value) => handleInputChange('campo2', value)}
            placeholder=""
          />
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.inputLabel}>Texto</Text>
          <TextInput
            style={styles.textInput}
            value={formData.campo3}
            onChangeText={(value) => handleInputChange('campo3', value)}
            placeholder=""
          />
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.inputLabel}>Texto</Text>
          <TextInput
            style={styles.textInput}
            value={formData.campo4}
            onChangeText={(value) => handleInputChange('campo4', value)}
            placeholder=""
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

export default AgregarDonante;