import React, { useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  SafeAreaView,
  FlatList,
} from 'react-native';
import Icon from 'react-native-vector-icons/MaterialIcons';
import { Picker } from '@react-native-picker/picker';

const HistorialEntregas = ({ navigation }) => {
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages] = useState(8); // Ejemplo: 8 páginas totales
  const [itemsPerPage, setItemsPerPage] = useState(10);

  // Datos de ejemplo para el historial de entregas
  const [entregas] = useState([
    {
      id: 1,
      evento: 'Entrega Navideña 2023',
      cantidadPaquetes: 150,
      fecha: '2023-12-24',
    },
    {
      id: 2,
      evento: 'Ayuda Comunitaria',
      cantidadPaquetes: 75,
      fecha: '2023-11-15',
    },
    {
      id: 3,
      evento: 'Donación Escolar',
      cantidadPaquetes: 200,
      fecha: '2023-10-20',
    },
    {
      id: 4,
      evento: 'Apoyo Familiar',
      cantidadPaquetes: 120,
      fecha: '2023-09-30',
    },
    {
      id: 5,
      evento: 'Entrega Mensual',
      cantidadPaquetes: 90,
      fecha: '2023-09-01',
    },
    {
      id: 6,
      evento: 'Ayuda de Emergencia',
      cantidadPaquetes: 180,
      fecha: '2023-08-15',
    },
  ]);

  const handlePreviousPage = () => {
    if (currentPage > 1) {
      setCurrentPage(currentPage - 1);
    }
  };

  const handleNextPage = () => {
    if (currentPage < totalPages) {
      setCurrentPage(currentPage + 1);
    }
  };

  const renderEntregaRow = ({ item }) => (
    <View style={styles.tableRow}>
      <View style={styles.eventoCell}>
        <Text style={styles.cellText}>{item.evento}</Text>
      </View>
      <View style={styles.cantidadCell}>
        <Text style={styles.cellText}>{item.cantidadPaquetes}</Text>
      </View>
      <View style={styles.fechaCell}>
        <Text style={styles.cellText}>{item.fecha}</Text>
      </View>
    </View>
  );

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity 
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Icon name="arrow-back" size={24} color="#000" />
        </TouchableOpacity>
        <Text style={styles.title}>Historial de{'\n'}entregas</Text>
      </View>

      <View style={styles.content}>
        {/* Table Header */}
        <View style={styles.tableHeader}>
          <View style={styles.eventoHeaderCell}>
            <Text style={styles.headerText}>Evento</Text>
          </View>
          <View style={styles.cantidadHeaderCell}>
            <Text style={styles.headerText}>Cantidad de{'\n'}paquetes</Text>
          </View>
          <View style={styles.fechaHeaderCell}>
            <Text style={styles.headerText}>Fecha</Text>
          </View>
        </View>

        {/* Table Content */}
        <View style={styles.tableContainer}>
          <FlatList
            data={entregas}
            renderItem={renderEntregaRow}
            keyExtractor={(item) => item.id.toString()}
            showsVerticalScrollIndicator={false}
            ListEmptyComponent={
              <View style={styles.emptyContainer}>
                <Text style={styles.emptyText}>No hay entregas registradas</Text>
              </View>
            }
          />
        </View>

        {/* Pagination */}
        <View style={styles.paginationContainer}>
          <View style={styles.pickerContainer}>
            <Picker
              selectedValue={itemsPerPage}
              style={styles.picker}
              onValueChange={(itemValue) => setItemsPerPage(itemValue)}
            >
              <Picker.Item label="5" value={5} />
              <Picker.Item label="10" value={10} />
              <Picker.Item label="20" value={20} />
              <Picker.Item label="50" value={50} />
            </Picker>
          </View>

          <View style={styles.pageNavigation}>
            <TouchableOpacity 
              style={styles.paginationButton}
              onPress={handlePreviousPage}
              disabled={currentPage === 1}
            >
              <Icon 
                name="chevron-left" 
                size={20} 
                color={currentPage === 1 ? '#ccc' : '#666'} 
              />
            </TouchableOpacity>
            
            <Text style={styles.paginationText}>
              Página {currentPage} de {totalPages}
            </Text>
            
            <TouchableOpacity 
              style={styles.paginationButton}
              onPress={handleNextPage}
              disabled={currentPage === totalPages}
            >
              <Icon 
                name="chevron-right" 
                size={20} 
                color={currentPage === totalPages ? '#ccc' : '#666'} 
              />
            </TouchableOpacity>
          </View>
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
    lineHeight: 22,
  },
  content: {
    flex: 1,
    margin: 16,
    backgroundColor: '#fff',
    borderRadius: 8,
    padding: 16,
  },
  tableHeader: {
    flexDirection: 'row',
    backgroundColor: '#e0e0e0',
    paddingVertical: 12,
    paddingHorizontal: 8,
    borderRadius: 4,
    marginBottom: 2,
    borderWidth: 1,
    borderColor: '#ccc',
  },
  eventoHeaderCell: {
    flex: 2,
    alignItems: 'center',
    borderRightWidth: 1,
    borderRightColor: '#ccc',
  },
  cantidadHeaderCell: {
    flex: 1.5,
    alignItems: 'center',
    borderRightWidth: 1,
    borderRightColor: '#ccc',
  },
  fechaHeaderCell: {
    flex: 1,
    alignItems: 'center',
  },
  headerText: {
    fontSize: 11,
    fontWeight: '500',
    color: '#333',
    textAlign: 'center',
    lineHeight: 14,
  },
  tableContainer: {
    flex: 1,
    backgroundColor: '#f8f8f8',
    borderWidth: 1,
    borderColor: '#ccc',
    borderTopWidth: 0,
  },
  tableRow: {
    flexDirection: 'row',
    backgroundColor: '#fff',
    paddingVertical: 12,
    paddingHorizontal: 8,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
    alignItems: 'center',
    minHeight: 50,
  },
  eventoCell: {
    flex: 2,
    alignItems: 'center',
    borderRightWidth: 1,
    borderRightColor: '#e0e0e0',
    paddingRight: 8,
  },
  cantidadCell: {
    flex: 1.5,
    alignItems: 'center',
    borderRightWidth: 1,
    borderRightColor: '#e0e0e0',
  },
  fechaCell: {
    flex: 1,
    alignItems: 'center',
    paddingLeft: 8,
  },
  cellText: {
    fontSize: 12,
    color: '#333',
    textAlign: 'center',
  },
  emptyContainer: {
    padding: 40,
    alignItems: 'center',
  },
  emptyText: {
    fontSize: 14,
    color: '#999',
    textAlign: 'center',
  },
  paginationContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 16,
    borderTopWidth: 1,
    borderTopColor: '#e0e0e0',
  },
  pickerContainer: {
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 4,
    width: 80,
    height: 40,
  },
  picker: {
    width: 80,
    height: 40,
  },
  pageNavigation: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  paginationButton: {
    padding: 8,
  },
  paginationText: {
    fontSize: 12,
    color: '#666',
    marginHorizontal: 12,
  },
});

export default HistorialEntregas;