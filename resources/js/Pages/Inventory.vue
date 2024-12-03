<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CategoriesModal from "@/Components/CategoriesModal.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import { Head } from '@inertiajs/vue3';
import { ChevronDownIcon } from 'lucide-vue-next'

const products = ref([]);
const showAddProductModal = ref(false);
const showCategoriesModal = ref(false);
const showEditProductModal = ref(false);
const listedCategories = ref([]);
const searchQuery = ref('');
const imagePreviewUrl = ref(null); 
const editImagePreviewUrl = ref(null);


//ERROR TRAPPING
const priceError = ref('');
const stockError = ref('');
const soldError = ref('');
const onsaleError = ref('');

const UpdatepriceError = ref('');
const UpdatestockError = ref('');
const UpdatesoldError = ref('');
const UpdateonsaleError = ref('');

const validatePrice = () => {
    if (!/^\d*\.?\d+$/.test(newProduct.value.price)) {
        priceError.value = "Please input only numbers for the Price field";
    } else {
        priceError.value = '';
    }
};

const validateStock = () => {
    if (!/^\d+$/.test(newProduct.value.stock)) {
        stockError.value = "Please input only positive whole numbers for the Stock field";
    } else {
        stockError.value = '';
    }
};

const validateSold = () => {
    if (!/^\d+$/.test(newProduct.value.sold)) {
        soldError.value = "Please input only positive whole numbers for the Sold field";
    } else {
        soldError.value = '';
    }
};

const validateOnsaleError = () => {
    if (!/^\d*\.?\d+$/.test(newProduct.value.on_sale_price)) {
        onsaleError.value = "Please input only numbers for the On Sale Price field";
    } else {
        onsaleError.value = '';
    }
};

const validateUpdatePrice = () => {
    if (!/^\d*\.?\d+$/.test(editProduct.value.price)) {
        UpdatepriceError.value = "Please input only numbers for the Price field";
    } else {
        UpdatepriceError.value = '';
    }
};

const validateUpdateStock = () => {
    if (!/^\d+$/.test(editProduct.value.stock)) {
        UpdatestockError.value = "Please input only positive whole numbers for the Stock field";
    } else {
        UpdatestockError.value = '';
    }
};

const validateUpdateSold = () => {
    if (!/^\d+$/.test(editProduct.value.sold)) {
        UpdatesoldError.value = "Please input only positive whole numbers for the Sold field";
    } else {
        UpdatesoldError.value = '';
    }
};

const validateUpdateOnsaleError = () => {
    if (!/^\d*\.?\d+$/.test(editProduct.value.on_sale_price)) {
        UpdateonsaleError.value = "Please input only numbers for the On Sale Price field";
    } else {
        UpdateonsaleError.value = '';
    }
};



const validationErrors = ref({
    name: '',
    price: '',
    category: '',
    status: '',
    brand: '',
    description: '',
    expDate: '',
});

const validateForm = () => {
    let isValid = true;
    // Reset validation errors
    for (const key in validationErrors.value) {
        validationErrors.value[key] = '';
    }

    if (!newProduct.value.name) {
        validationErrors.value.name = 'This field is required';
        isValid = false;
    }
    if (!newProduct.value.price) {
        validationErrors.value.price = 'This field is required';
        isValid = false;
    }
    if (!newProduct.value.category) {
        validationErrors.value.category = 'This field is required';
        isValid = false;
    }
    if (!newProduct.value.status) {
        validationErrors.value.status = 'This field is required';
        isValid = false;
    }
    if (!newProduct.value.brand) {
        validationErrors.value.brand = 'This field is required';
        isValid = false;
    }
    if (!newProduct.value.description) {
        validationErrors.value.description = 'This field is required';
        isValid = false;
    }
    if (!newProduct.value.expDate) {
        validationErrors.value.expDate = 'This field is required';
        isValid = false;
    }

    const allowedImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (newProduct.value.image && !allowedImageTypes.includes(newProduct.value.image.type)) {
        alert('Accepted file format is jpeg, jpg, or png.');
        validationErrors.value.image = 'Accepted file format is jpeg, jpg, or png.';
        isValid = false;
    }

    return isValid;
};

const validationErrorsEdit = ref({
    name: '',
    price: '',
    category: '',
    status: '',
    brand: '',
    description: '',
    expDate: '',
});

const validateEditForm = () => {
    let isValid = true;
    // Reset validation errors
    for (const key in validationErrorsEdit.value) {
        validationErrorsEdit.value[key] = '';
    }

    if (!editProduct.value.name) {
        validationErrorsEdit.value.name = 'This field is required';
        isValid = false;
    }
    if (!editProduct.value.price) {
        validationErrorsEdit.value.price = 'This field is required';
        isValid = false;
    }
    if (!editProduct.value.category) {
        validationErrorsEdit.value.category = 'This field is required';
        isValid = false;
    }
    if (!editProduct.value.status) {
        validationErrorsEdit.value.status = 'This field is required';
        isValid = false;
    }
    if (!editProduct.value.brand) {
        validationErrorsEdit.value.brand = 'This field is required';
        isValid = false;
    }
    if (!editProduct.value.description) {
        validationErrorsEdit.value.description = 'This field is required';
        isValid = false;
    }
    if (!editProduct.value.expDate) {
        validationErrorsEdit.value.expDate = 'This field is required';
        isValid = false;
    }

    return isValid;
};

const editProduct = ref({
    id: null,
    name: '',
    brand: '',
    price: 0,
    category: '',
    stock: 0,
    sold: 0,
    status: '',
    expDate: '',
    image: null,
    description: '',
    on_sale: null,
    on_sale_price: 0,
    featured: 'null'
});

watch(() => editProduct.value.on_sale, (newValue) => {
    if (newValue === 'no') {
        editProduct.value.on_sale_price = 0;
    }
});

const fetchProducts = async () => {
    try {
        const response = await axios.get('/api/products');
        products.value = response.data;
    } catch (error) {
        console.error("Error fetching products:", error);
    }
};

const fetchListedCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        listedCategories.value = response.data.filter(category => category.status === 'listed');
    } catch (error) {
        console.error("Error fetching categories:", error);
    }
};



const newProduct = ref({
    name: '',
    brand: '',
    price: 0,
    category: '',
    stock: 0,
    sold: 0,
    status: '',
    expDate: '',
    image: null,
    description: '',
    on_sale: 'no',
    on_sale_price: 0,
    featured: 'false',
});


const addProduct = async () => {
    if (!validateForm()) return;

    try {
        const formData = new FormData();
        for (const key in newProduct.value) {
            if (newProduct.value[key] !== null) {
                formData.append(key, newProduct.value[key]);
            }
        }
        const response = await axios.post('/api/products', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        showSuccessAddModal.value = true;
        setTimeout(() => {
        showSuccessAddModal.value = false
        }, 1000) // Auto-close after 2 seconds

        products.value.push(response.data.product);
        showAddProductModal.value = false;
        resetNewProduct();
        imagePreviewUrl.value = null; 
    } catch (error) {
        console.error("Error adding product:", error);
    }
};

const validateProduct = (product) => {
    if (!product.name || !product.brand || !product.price || !product.category || !product.stock || !product.sold || !product.status || !product.expDate || !product.description) {
        return false;
    }
    return true;
};

const updateProduct = async () => {
    if (!validateEditForm()) return;

    try {
        const formData = new FormData();

        
        for (const key in editProduct.value) {
            if (key !== 'image' && editProduct.value[key] !== null) {
                formData.append(key, editProduct.value[key]);
            }
        }

        
        if (editProduct.value.image instanceof File) {
            formData.append('image', editProduct.value.image);
        }

        const response = await axios.post(`/api/products/${editProduct.value.id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-HTTP-Method-Override': 'PUT',
            }
        });

        
        showSuccessEditModal.value = true;
        setTimeout(() => {
        showSuccessEditModal.value = false
        }, 1000) // Auto-close after 2 seconds

        const index = products.value.findIndex(product => product.id === editProduct.value.id);
        products.value[index] = response.data.product;
        fetchProducts();
        showEditProductModal.value = false;
        resetEditProduct();
        
    } catch (error) {
        if (error.response && error.response.data) {
            console.error("Validation errors:", error.response.data);
        } else {
            console.error("Error updating product:", error);
        }
    }
};

const deleteProduct = async (id) => {
    if (confirm("Are you sure you want to delete this product?")) {
        try {
            await axios.delete(`/api/products/${id}`);
            products.value = products.value.filter(product => product.id !== id);
        } catch (error) {
            console.error("Error deleting product:", error);
        }
    }
};

const editProductDetails = (product) => {
    editProduct.value = { ...product };

    editImagePreviewUrl.value = product.image ? `/storage/${product.image}` : null;

    
    const fileInput = document.getElementById('edit_image');
    if (fileInput) {
        fileInput.value = ''; 
    }

    showEditProductModal.value = true;
};

const cancelAddProduct = () => {
    showAddProductModal.value = false;
    resetNewProduct();
    imagePreviewUrl.value = null;
};

const resetNewProduct = () => {
    newProduct.value = {
        name: '',
        brand: '',
        price: 0,
        category: '',
        stock: 0,
        sold: 0,
        status: '',
        expDate: '',
        image: null,
        description: '',
        on_sale: 'no',
        on_sale_price: 0,
        featured: 'false'
    };
};

const resetEditProduct = () => {
    editProduct.value = {
        id: null,
        name: '',
        brand: '',
        price: 0,
        category: '',
        stock: 0,
        sold: 0,
        status: '',
        expDate: '',
        image: null,
        description: '',
        on_sale: null,
        on_sale_price: 0,
        featured: 'false'
    };
    editImagePreviewUrl.value = null; 
    const fileInput = document.getElementById('edit_image');
    if (fileInput) {
        fileInput.value = ''; 
    }
};

const filteredProducts = computed(() => {
    if (!searchQuery.value) {
        return products.value;
    }
    return products.value.filter(product => {
        const searchTerm = searchQuery.value.toLowerCase();
        return (
            product.name.toLowerCase().includes(searchTerm) ||
            product.category.toLowerCase().includes(searchTerm) ||
            product.status.toLowerCase().includes(searchTerm) || 
            product.brand.toLowerCase().includes(searchTerm)
        );
    });
});

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    const maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
    if (file) {
        // Check file type
        if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
            alert('Accepted file format is jpeg, jpg, or png.');
            event.target.value = ''; // Clear the file input
            newProduct.value.image = null; // Reset the image value
            return;
        }

        // Check file size
        if (file.size > maxFileSize) {
            alert('The maximum file size for image is 5MB.');
            event.target.value = ''; // Clear the file input
            newProduct.value.image = null; // Reset the image value
            return;
        }

        // If valid, set the image
        newProduct.value.image = file;
        imagePreviewUrl.value = URL.createObjectURL(file);
    }
};


const handleEditImageUpload = (event) => {
    const file = event.target.files[0];
    const maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
    if (file) {
        // Check file type
        if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
            alert('Accepted file format is jpeg, jpg, or png.');
            event.target.value = ''; // Clear the file input
            editProduct.value.image = null; // Reset the image value
            return;
        }

        // Check file size
        if (file.size > maxFileSize) {
            alert('The maximum file size for image is 5MB.');
            event.target.value = ''; // Clear the file input
            editProduct.value.image = null; // Reset the image value
            return;
        }

        // If valid, set the image
        editProduct.value.image = file;
        editImagePreviewUrl.value = URL.createObjectURL(file);
    }
};







const isDateFiltered = ref(false);
const startDate = ref('');
const endDate = ref('');
const productsByDate = ref([]);
watch([startDate, endDate], async ([newStartDate, newEndDate]) => {
    if (newStartDate && newEndDate) {
        // if(showPrintInvoiceSummaryByDate){
        //     isDateFiltered.value = false;
        // }
        await fetchInvoicesByDate();
    } else {
        // Reset financesByDate and isDateFiltered if dates are cleared
        productsByDate.value = [];
        isDateFiltered.value = false;
    }
});





const fileImport = ref(null);
const message = ref('');
const messageType = ref('');
const showImportExportModal = ref(false);

const showFileInput = ref(false);
const handleFileImportUpload = (event) => {
  fileImport.value = event.target.files[0];
    showFileInput.value=true;
};

const uploadFileImport = async () => {
  if (!fileImport.value) {
    message.value = 'Please select a file.';
    messageType.value = 'error';
    return;
  }

  let formData = new FormData();
  formData.append('file', fileImport.value);

  try {
    const response = await axios.post('/api/products/import/xlsx', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    message.value = response.data.message;
    messageType.value = 'success';
  } catch (error) {
    message.value = error.response.data.message;
    messageType.value = 'error';
    if (error.response.data.errors) {
      message.value += '\n' + error.response.data.errors.join('\n');
    }
  }
};




const closeModal = () => {
  showImportExportModal.value = false;
};






const summaryOption = ref({
  option: "",
});
watch(
  () => summaryOption.value.option, // Watch the specific value within the ref
  (newOption) => {
    console.log('NEW OPTION VALUE:', newOption);
    // Add additional logic here if needed
  }
);

function printInventorySummary() {
    try {
        // const startDate = startDatePrint.value;
        // const endDate = endDatePrint.value;
        
        // const categoriesString = selectedCategories.value.join(',');

        if(summaryOption.value.option === 'summaryPdf'){
            window.open(`/api/products/print/pdf`, '_blank');
        }   
        else{
            window.open(`/api/products/print/export/xlsx/`, '_blank');
        }

    } catch (error) {
        console.error("Error fetching invoices by date:", error);
    }
};

function printInventoryTemplate() {
    try {
        // const startDate = startDatePrint.value;
        // const endDate = endDatePrint.value;
            window.open(`/api/products/print/export/template_xslx/`, '_blank');
        

    } catch (error) {
        console.error("Error fetching invoices by date:", error);
    }
};


const showSuccessAddModal = ref(false);
const showSuccessModal = ref(false);
const showSuccessEditModal = ref(false);
const showSuccessNotifModal = ref(false);

const emit = defineEmits(['financeDeleted', 'editFinance', 'viewFinance'])

const showDeleteModal = ref(false)
let financeToDelete = 0;

const openDeleteModal = (id) => {
  financeToDelete = id
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  financeToDelete = null
}

const confirmDelete = async () => {
  try {
    await axios.delete(`/api/products/${financeToDelete}`)
    closeDeleteModal()
    fetchProducts();
    showSuccessModal.value = true
    emit('financeDeleted', financeToDelete)
    setTimeout(() => {
      showSuccessModal.value = false
    }, 1000) // Auto-close after 2 seconds



  } catch (error) {
    console.error("Error deleting finance:", error)
    // You might want to show an error message to the user here
  }
}

const showViewProductModal = ref('');
const viewProductDetails = (product) => {
    editProduct.value = { ...product };

    editImagePreviewUrl.value = product.image ? `/storage/${product.image}` : null;

    
    const fileInput = document.getElementById('edit_image');
    if (fileInput) {
        fileInput.value = ''; 
    }

    showViewProductModal.value = true;
};


function isDateNear(productExpDate) {
  const currentDate = new Date();
  const productExpDateObj = new Date(productExpDate);
  
  const daysDifference = Math.abs(
    (productExpDateObj - currentDate) / (1000 * 60 * 60 * 24)
  );

  return daysDifference <= (expiryDays.value || 7);
}

function isDateTodayOrPast(date) {
  const today = new Date().setHours(0, 0, 0, 0);
  const expiryDate = new Date(date).setHours(0, 0, 0, 0);
  return expiryDate <= today;
}

function formatDate(date) {
  return new Date(date).toLocaleDateString();
}


fetchProducts();
fetchListedCategories();


const isOpen = ref(false);
const stocksDays = ref(0);
const expiryDays = ref(0);

// Toggle dropdown visibility
const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

// Fetch notification settings from the API
onMounted(async () => {
    try {
        const response = await axios.get('/api/productNotif');
        const settings = response.data;

        // Map API data to the Vue component's variables
        settings.forEach(setting => {
            if (setting.stock_expDate === 'stock') {
                stocksDays.value = setting.count;
            } else if (setting.stock_expDate === 'expDate') {
                expiryDays.value = setting.count;
            }
        });
    } catch (error) {
        console.error('Error fetching product notification settings:', error);
    }
});

// Function to save updated values
const saveSettings = async () => {
    try {
        await axios.put('/api/productNotif', [
            { stock_expDate: 'stock', count: stocksDays.value },
            { stock_expDate: 'expDate', count: expiryDays.value }
        ]);
        fetchProducts;
        
        
        showSuccessNotifModal.value = true;
        setTimeout(() => {
        showSuccessNotifModal.value = false
        }, 500) // Auto-close after 2 seconds

    } catch (error) {
        console.error('Error saving product notification settings:', error);
        alert('Error saving settings');
    }
};

// Function to reset the input values
const cancelChanges = () => {
    // Reset values by re-fetching from the API
    onMounted();
    
    isOpen.value = false; // Close the dropdown
};

watch(
  () => expiryDays.value, // Watch the specific value within the ref
  (newValue) => {
    console.log('NEW STOCKDAYS VALUE:', newValue);

    // Add additional logic here if needed
  }
);
watch(
  () => stocksDays.value, // Watch the specific value within the ref
  (newValue) => {
    console.log('NEW STOCKDAYS VALUE:', newValue);

    // Add additional logic here if needed
  }
);


const isLowStock = (productStock) => {
    return productStock < stocksDays.value;
};

function sortById() {
    // Toggle the sort order
    sortOrderId.value = sortOrderId.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current sort order
    products.value.sort((a, b) => {
        if (sortOrderStock.value === 'asc') {
            return a.stock - b.stock;
        } else {
            return b.stock - a.stock;
        }
    });
}

const sortOrder = ref('asc'); // Default sort order
function sortByName() {
    // Toggle the sort order
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current sort order
    products.value.sort((a, b) => {
        if (sortOrder.value === 'asc') {
            return a.name.localeCompare(b.name);
        } else {
            return b.name.localeCompare(a.name);
        }
    });
}

const sortOrderBrand = ref('asc'); // Default sort order
function sortByBrand() {
    // Toggle the sort order
    sortOrderBrand.value = sortOrderBrand.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current sort order
    products.value.sort((a, b) => {
        if (sortOrderBrand.value === 'asc') {
            return a.brand.localeCompare(b.brand);
        } else {
            return b.brand.localeCompare(a.brand);
        }
    });
}



const sortOrderPrice = ref('asc'); // Default sort order
function sortByPrice() {
    // Toggle the sort order
    sortOrderPrice.value = sortOrderPrice.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current sort order
    products.value.sort((a, b) => {
        if (sortOrderPrice.value === 'asc') {
            return a.price - b.price;
        } else {
            return b.price - a.price;
        }
    });
}



const sortOrderCategory = ref('asc'); // Default sort order
function sortByCategory() {
    // Toggle the sort order
    sortOrderCategory.value = sortOrderCategory.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current sort order
    products.value.sort((a, b) => {
        if (sortOrderCategory.value === 'asc') {
            return a.category.localeCompare(b.category);
        } else {
            return b.category.localeCompare(a.category);
        }
    });
}

const sortOrderStock = ref('asc'); // Default sort order
function sortByStock() {
    // Toggle the sort order
    sortOrderStock.value = sortOrderStock.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current sort order
    products.value.sort((a, b) => {
        if (sortOrderStock.value === 'asc') {
            return a.stock - b.stock;
        } else {
            return b.stock - a.stock;
        }
    });
}

const sortOrderSold = ref('asc'); // Default sort order
function sortBySold() {
    // Toggle the sort order
    sortOrderSold.value = sortOrderSold.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current sort order
    products.value.sort((a, b) => {
        if (sortOrderSold.value === 'asc') {
            return a.sold - b.sold;
        } else {
            return b.sold - a.sold;
        }
    });
}

const sortOrderStatus = ref('asc'); // Default sort order
function sortByStatus() {
    // Toggle the sort order
    sortOrderStatus.value = sortOrderStatus.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current sort order
    products.value.sort((a, b) => {
        if (sortOrderStatus.value === 'asc') {
            return a.status.localeCompare(b.status);
        } else {
            return b.status.localeCompare(a.status);
        }
    });
}


const expDateSortOrder = ref('asc'); // Default sort order for Exp. Date

function sortByExpDate() {
    // Toggle the sort order for Exp. Date
    expDateSortOrder.value = expDateSortOrder.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current expDate sort order
    products.value.sort((a, b) => {
        const dateA = new Date(a.expDate);
        const dateB = new Date(b.expDate);

        if (expDateSortOrder.value === 'asc') {
            return dateA - dateB; // Sort ascending by date (earliest first)
        } else {
            return dateB - dateA; // Sort descending by date (latest first)
        }
    });
}



// const colNameIsVisible = computed({
//   get: () => itemsTable.value.find(item => item.id === 'item1')?.checked || true,
//   set: value => {
//     const item = itemsTable.value.find(item => item.id === 'item1');
//     if (item) item.checked = value;
//   },
// });
// State to manage dropdown visibility
const productsTableVisibility = ref({
  colIdIsVisible: false,
  colImageIsVisible: false,
  colNameIsVisible: false,
  colBrandIsVisible: false,
  colPriceIsVisible: false,
  colCategoryIsVisible: false,
  colStockIsVisible: false,
  colSoldIsVisible: false,
  colStatusIsVisible: false,
  colExpiryIsVisible: false,
});

// Map API column_Table names to internal state keys
const columnMapping = {
  productId: "colIdIsVisible",
  productImage: "colImageIsVisible",
  productName: "colNameIsVisible",
  productBrand: "colBrandIsVisible",
  productPrice: "colPriceIsVisible",
  productCategory: "colCategoryIsVisible",
  productStock: "colStockIsVisible",
  productSold: "colSoldIsVisible",
  productStatus: "colStatusIsVisible",
  productExpiry: "colExpiryIsVisible",
};

// Define the table items dynamically based on visibility
const itemsTable = computed(() =>
  Object.keys(productsTableVisibility.value).map((key, index) => ({
    id: `item${index + 1}`,
    label: key.replace("col", "").replace("IsVisible", ""), // Adjust to create a user-friendly label
    stateKey: key,
    checked: productsTableVisibility.value[key], // Always sync with state
  }))
);

// Dropdown visibility state
const isOpenTable = ref(false);

// Toggle dropdown visibility
const toggleDropdownTable = () => {
  isOpenTable.value = !isOpenTable.value;
  console.log("Dropdown is now:", isOpenTable.value ? "Open" : "Closed");
};

// Fetch product column visibility from the API
const fetchProductColumnTableVisibility = async () => {
  try {
    const response = await axios.get("/api/productVis");

    // Process API response and update visibility states
    const visibilityData = response.data;
    visibilityData.forEach((item) => {
      const columnKey = columnMapping[item.column_Table];
      if (columnKey) {
        productsTableVisibility.value[columnKey] = !!item.is_visible; // Convert `1`/`0` to `true`/`false`
      }
    });

    console.log("Updated productsTableVisibility:", productsTableVisibility.value);
  } catch (error) {
    console.error("Error fetching product column table visibility:", error);
  }
};


const toggleVisibility = (item) => {
  const key = item.stateKey;
  if (key && key in productsTableVisibility.value) {
    productsTableVisibility.value[key] = !productsTableVisibility.value[key]; // Update state
    console.log(`Visibility for ${key} updated to`, productsTableVisibility.value[key]);
    console.log("NEW VALUES ON THE ENTIRE PRODUCTSTABLEVISIBILITY", productsTableVisibility)
  }
};

const cancelColumnVisibilityChanges = () => {
    // Reset values by re-fetching from the API
    isOpenTable.value = false; // Close the dropdown
};
const saveColumnVisibilitySettings = async () => {
    try {
        await axios.put('/api/productVis', [
        { column_Table: 'productId', is_visible: productsTableVisibility.value.colIdIsVisible },
        { column_Table: 'productImage', is_visible: productsTableVisibility.value.colImageIsVisible },
        { column_Table: 'productName', is_visible: productsTableVisibility.value.colNameIsVisible },
        { column_Table: 'productBrand', is_visible: productsTableVisibility.value.colBrandIsVisible },
        { column_Table: 'productPrice', is_visible: productsTableVisibility.value.colPriceIsVisible },
        { column_Table: 'productCategory', is_visible: productsTableVisibility.value.colCategoryIsVisible },
        { column_Table: 'productStock', is_visible: productsTableVisibility.value.colStockIsVisible },
        { column_Table: 'productSold', is_visible: productsTableVisibility.value.colSoldIsVisible },
        { column_Table: 'productStatus', is_visible: productsTableVisibility.value.colStatusIsVisible },
        { column_Table: 'productExpiry', is_visible: productsTableVisibility.value.colExpiryIsVisible }
        ]);
        fetchProductColumnTableVisibility;
        
        
        showSuccessNotifModal.value = true;
        setTimeout(() => {
        showSuccessNotifModal.value = false
        }, 500) // Auto-close after 2 seconds

    } catch (error) {
        console.error('Error saving product column visibility settings:', error);
        alert('Error saving settings');
    }
};





const newInvoiceComputation = ref({
    total_Amount_Due: 0,

    
})

    const showAddPackageModal = ref(false);
    const showEditPackageModal = ref(false);
    
    const filteredProductsForPackage = computed(() => (query) => {
    if (!query) {
        return [];
    }
    const searchTerm = query.toLowerCase();
    return products.value.filter((product) => {
        return (
        product.name.toLowerCase().includes(searchTerm) ||
        product.category.toLowerCase().includes(searchTerm) ||
        product.status.toLowerCase().includes(searchTerm) ||
        product.brand.toLowerCase().includes(searchTerm)
        );
    });
    });

    const textItemFields = ref([
    { 
        product_id:0, stock: '', sold:'', image:'', searchProductQuery: '', on_sale: 'no', amount: '', quantity: '', total_amount: '', areFieldsEnabled: false, isSearching: false }
    ]);// Start with one pair of text fields

    // Method to add a new pair of text fields
    const addItemTextField = () => {
    textItemFields.value.push({
        product_id:0, stock: '', sold:'', image:'', searchProductQuery: '', on_sale: 'no', amonunt: '', quantity: '', total_amount: '', areFieldsEnabled: false, isSearching: false, 
    });
    newInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
    };
    // Method to remove a text field
    const removeItemTextField = (index) => {
        newInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
    textItemFields.value.splice(index, 1); // Remove the text field pair at the specified index
    };
    const selectProduct = (product, index) => {
    // Assign the selected product's details to the corresponding text field
    textItemFields.value[index].stock = product.stock;
    textItemFields.value[index].product_id = product.id;
    textItemFields.value[index].image = product.image;
    textItemFields.value[index].searchProductQuery = product.name;
    textItemFields.value[index].on_sale = product.on_sale;
    // Hide the search results (clear the search query)
    textItemFields.value[index].amount = product.price;
    textItemFields.value[index].areFieldsEnabled = true;
    textItemFields.value[index].sold = product.sold;
    textItemFields.value[index].isSearching = false; // Hide the search results 

    console.log('Selected Product ID:', textItemFields.value[index].product_id)
    console.log('Selected Product Image:', textItemFields.value[index].image)
    };


    function validateKeyPress(event) {
  // Allow numbers (0-9) and the period/dot character
  if (!/^[0-9.]$/.test(event.key)) {
    event.preventDefault();
  }
}

const roundToTwoDecimals = (num) => Math.round(num * 100) / 100;
const updateTotalProductAmount = (index) => {
    const field = textItemFields.value[index];
  field.total_amount = roundToTwoDecimals(roundToTwoDecimals(field.quantity) * roundToTwoDecimals(field.amount) || 0);
  newInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
};

const newPackage = reactive({
    product_package_id: '',
    product_package_name: '',
});

watch(
      () => newPackage.product_package_name, // Getter function for the watched property
      (newValue, oldValue) => {
        console.log('New Value:', newValue);
        console.log('Old Value:', oldValue);
        // You can add additional logic here
      }
    );
    
let responsePackageId = ref(0);
const addProductPackage = async () => {
    try {
        
        if(newPackage.product_package_name && textItemFields){
            console.log('IT WILL ADD THE PACKAGE TITE TITE TITE');

            try {
                const formData = new FormData();
                formData.append('product_package_name', newPackage.product_package_name);

                console.log([...formData.entries()]); 
                console.log('NEW PACKAGE PRODUCT NAME: ', newPackage.product_package_name);

                // Make the POST request
                const response = await axios.post('/api/productPackageName', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });

                // Extract the product_package_id from the response
                responsePackageId.value = response.data.id;
                
                console.log('Newly created rsponse package:', response.data);
                console.log('Newly created product_package_id:', responsePackageId.value);
            } catch (error) {
                console.error('Error adding package name:', error);
            }


            let loop = 0;
            for (let field of textItemFields.value) {
                    loop += 1;     

                    console.log('PRODUCT ID TO BE ADDED', field.product_id)
                    console.log('PRODUCT NAME TO BE ADDED', field.searchProductQuery)

                    if (field.searchProductQuery.trim() !== '') {
                        const formData = new FormData();
                        formData.append('product_package_id', responsePackageId.value);
                        formData.append('product_id', field.product_id);
                        formData.append('product_name', field.searchProductQuery);
                        formData.append('product_quantity', field.quantity);

                        
                    console.log('PRODUCT NAME TO BE ADDED', formData)

                        try {
                            // First, post the invoice item
                            await axios.post('/api/productPackage', formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            });

                        } catch (error) {
                            console.error("Error updating stock or posting products in the package name:", error);
                        }
                    } else {
                        console.log('Skipping empty item description');
                    }
                
            }


            fetchPackageData();
            return;
        }
        fetchPackageData();
        console.log("Invoice Item added successfully.");
    } catch (error) {
        console.error("Error adding invoice item:", error);
    }
}


    fetchProductColumnTableVisibility();    

const isOpenTest = ref(false)
const selectedOptionTest = ref('')
const optionsTest = ['List of Products', 'List of Packages']

const isPackageTableShowing = ref(false);
const isProductTableShowing = ref(true);

const toggleDropdownTest = () => {
  isOpenTest.value = !isOpenTest.value
}

const selectOptionTest = (option) => {
  selectedOptionTest.value = option
  
  if(selectedOptionTest.value === "List of Products"){
    isProductTableShowing.value = true;
    isPackageTableShowing.value = false;
    console.log("LIST OF PRODUCTS SHOWING")
  }
  else{
    isPackageTableShowing.value = true;
    isProductTableShowing.value = false;
    console.log("LIST OF PACKAGES SHOWING")
  }
  console.log(`Selected option: ${option}`) // Logs the selected option
  isOpenTest.value = false
}


const packageData = ref([]);

const fetchPackageData = async () => {
  try {
    const response = await axios.get('/api/productPackage')
    packageData.value = response.data
    console.log('Fetched package data:', packageData.value)
    
    // Log each package and its products
    packageData.value.forEach((pkg, index) => {
      console.log(`Package ${index + 1}:`, pkg.package_name?.product_package_name)
      console.log('Products:', pkg.products)
    })
  } catch (error) {
    console.error('Error fetching package data:', error)
  }
}

const groupedPackages = computed(() => {
  const grouped = {}
  packageData.value.forEach(item => {
    const packageName = item.package_name?.product_package_name || 'No Package Name'
    if (!grouped[packageName]) {
      grouped[packageName] = []
    }
    grouped[packageName].push(item)
  })
  console.log('Grouped Packages:', grouped)
  return grouped
})

const showSuccessAddPackageModal = ref(false);
const showSuccessPackageModal = ref(false);
const showSuccessEditPackageModal = ref(false);
const showSuccessNotifPackageModal = ref(false);


const showDeletePackageModal = ref(false)

const openDeletePackageModal = (id) => {
  financeToDelete= id
  showDeletePackageModal.value = true
}

const closeDeletePackageModal = () => {
  showDeletePackageModal.value = false
  financeToDelete = null
}

const confirmDeletePackage = async () => {
  try {
    await axios.delete(`/api/productPackageName/${financeToDelete}`);    
    closeDeletePackageModal()
    fetchPackageData();
    showSuccessPackageModal.value = true
    emit('financeDeleted', financeToDelete)
    setTimeout(() => {
      showSuccessPackageModal.value = false
    }, 1000) // Auto-close after 2 seconds
  } catch (error) {
    console.error("Error deleting product package:", error)
    // You might want to show an error message to the user here
  }
}

fetchPackageData();

const updatePackage = ref({});
const updatePackageItems = ref([]);
const productsPackage = ref([]);
let edit_invoice_system_id = 0;

watch(
  () => updatePackage.value.product_package_name,
  (newVal, oldVal) => {
    console.log(`Package name changed from "${oldVal}" to "${newVal}"`);

    // Check if the new value is empty after trimming whitespace
    if (newVal.trim() === '') {
      console.warn('Package name cannot be empty!');
    }
  }
);

const editPackage = async (packageItem) => {
    try {
        // Assuming `invoice_id` is passed when calling this method
        const response = await axios.get(`/api/productPackageName/${packageItem.package_name.id}`);
        updatePackage.value = response.data;
        showEditPackageModal.value = true;


        const responseItems = await axios.get(`api/productPackage/${packageItem.package_name.id}`);
        const responseProducts = await axios.get(`api/products`);
        
        console.log('UPDATING PACKAGE: ', responseItems.data)
        productsPackage.value = responseProducts.data;

        // Map over the selectedInvoiceItems and add the product amount
        selectedInvoiceItems.value = responseItems.data.map(item => {
            const product = productsPackage.value.find(p => p.id === item.product_id);
            return {
                ...item,
                amount: product ? parseFloat(product.price) : 0
            };
        });

        console.log('all products', productsPackage.value);
        console.log('selected invoice items', selectedInvoiceItems.value);

    } catch (error) {
        console.error("Error fetching product package details:", error);
    }
};

const updateProductPackage = async () => {
  try {
    console.log('CLICKED UPDATING PRODUCT PACKAGE:')


    try {
        // Create a FormData object and append the necessary fields
        const formData = new FormData();
            formData.append('product_package_name', updatePackage.value.product_package_name);
        // Use the invoice_system_id from editInvoice for the request URL
        const response = await axios.post(`/api/productPackageName/${updatePackage.value.id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        });
    } catch (error) {
    console.error("Error updating invoice:", error);
    }

    
    
    // Send DELETE request to remove all invoice items for the given invoice_system_id
    await axios.delete(`/api/productPackage/${updatePackage.value.id}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });

    // Optional: update front-end state to remove the deleted invoice items from the list


    for (let field of selectedInvoiceItems.value) {
      // Only process new items

        if (field.product_name.trim() !== '') {
            const formData = new FormData();

                formData.append('product_package_id', updatePackage.value.id);
                formData.append('product_id', field.product_id);
                formData.append('product_name', field.product_name);
                formData.append('product_quantity', field.product_quantity);

            await axios.post(`/api/productPackage/${updatePackage.value.id}`, formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            });
            
        } else {
          console.log('Skipping empty item description');
        }
      
    }
        fetchPackageData();

        showEditPackageModal.value = false; 
        showSuccessEditPackageModal.value = true;
        setTimeout(() => {
        showSuccessEditPackageModal.value = false
        }, 1000) // Auto-close after 2 seconds


    // console.log("Invoice Items deleted successfully.");
  } catch (error) {
    console.error("Error updating invoice items:", error);
  }
};




const filteredUpdateProducts = (query) => {
  if (!query) {
    return [];
  }
  const searchTerm = query.toLowerCase();
  return products.value.filter((product) => {
    return (
      product.name.toLowerCase().includes(searchTerm) ||
      product.category.toLowerCase().includes(searchTerm) ||
      product.status.toLowerCase().includes(searchTerm) ||
      product.brand.toLowerCase().includes(searchTerm)
    );
  });
};

const editInvoiceComputation = ref({
    total_Amount_Due: 0,
})
const selectedInvoiceItems = ref([
    { 
        product_id:0, stock: '', sold:'', image:'', searchProductQuery: '', on_sale: 'no', amount: '', quantity: '', total_amount: '', areFieldsEnabled: false, isSearching: false }
    ]);
    
const addUpdateItemTextField = () => {
    selectedInvoiceItems.value.push({
        product_id:0, stock: '', sold:'', image:'', searchProductQuery: '', on_sale: 'no', amonunt: '', quantity: '', total_amount: '', areFieldsEnabled: false, isSearching: false, 
    });
    console.log('ADDING UPDATE ITEM TEXT FIELD');
    editInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
};

watch(
  selectedInvoiceItems,
  async (newItems) => {
    for (let i = 0; i < newItems.length; i++) {
      const newProductId = newItems[i].product_id;

      // Fetch stock if product_id is set
      if (newProductId) {
        try {
          const response = await axios.get(`/api/products/${newProductId}/stock`);
          selectedInvoiceItems.value[i].stock = response.data.stock;
        } catch (error) {
          console.error(`Error fetching stock for product ${newProductId}:`, error);
          selectedInvoiceItems.value[i].stock = 0; // Default if error
        }
      }
    }
  },
  { deep: true }
);


watch(
  selectedInvoiceItems,
  (newItems) => {
    for (let item of newItems) {
      if (item.oldQuantity === '' && item.quantity !== '') {
        item.oldQuantity = item.quantity;  // Set oldQuantity only once
        console.log(`Initial oldQuantity set for product_id ${item.product_id}:`, item.oldQuantity);
      }
    }
  },
  { deep: true }
);

const oldStock = ref();
const removeItemTextField1 = (index) => {
    selectedInvoiceItems.value.splice(index, 1); // Remove the text field pair at the specified index
  console.log('edit remove text field clicked');
  editInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
};
const selectUpdateProduct = async (product, index) => {
  // Assign the selected product's details to the corresponding text field
  selectedInvoiceItems.value[index].product_id = product.id;
  selectedInvoiceItems.value[index].sold = product.sold;

  selectedInvoiceItems.value[index].image = product.image;
  selectedInvoiceItems.value[index].product_name = product.name;
  selectedInvoiceItems.value[index].on_sale = product.on_sale;
  // Hide the search results (clear the search query)
  selectedInvoiceItems.value[index].amount = product.price;
  selectedInvoiceItems.value[index].areFieldsEnabled = true;
  selectedInvoiceItems.value[index].isSearching = false; // Hide the search results 
  selectedInvoiceItems.value[index].quantity = 0;
  selectedInvoiceItems.value[index].final_price = 0;
  selectedInvoiceItems.value[index].stock = product.stock;
  selectedInvoiceItems.value[index].oldQuantity = 0;

  oldStock.value = product.stock;
  try {
    const response = await axios.get(`/api/products/${product.id}/stock`);
    selectedInvoiceItems.value[index].stock = response.data.stock;
  } catch (error) {
    console.error('Error fetching product stock:', error);
    selectedInvoiceItems.value[index].stock = 0; // Set default stock if there's an error
  }
  console.log('TANG INA ANO BA YUNG PANGALAN MO HAHAHAHA', selectedInvoiceItems.value[index].stock)
};




const updateTotalProductAmountUpdate = (index) => {
const field = selectedInvoiceItems.value[index];
field.final_price = roundToTwoDecimals(roundToTwoDecimals(field.quantity) * roundToTwoDecimals(field.amount) || 0);
console.log('updateTotalProductAmountUpdate called');
editInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
};




watch(
  () => updatePackage.product_package_name,
  (newVal, oldVal) => {
    console.log(`Package name changed from "${oldVal}" to "${newVal}"`);

    // Check if the new value is empty after trimming whitespace
    if (newVal.trim() === '') {
      console.warn('Package name cannot be empty!');
    }
  }
);




</script>

<template>
    <Head title="Inventory" />
    <AuthenticatedLayout>

        <div  class="py-5 h-full">
            <div class="max-w-auto h-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg h-[86%]">
                    <div class="p-6 h-full text-gray-900 dark:text-gray-100 flex flex-col">
                        <div class="mt-4 mb-8 flex justify-between items-center mb-4">

                            <div class="z-30 relative inline-block tesxt-left">
                                <div>
                                <button 
                                    type="button" 
                                    class="hover:bg-gray-50 transition-colors duration-150 ease-in-out text-white text-4xl font-semibold inline-flex justify-center items-center w-full px-4 py-2 hover:text-gray-900 focus:outline-none"
                                    @click="toggleDropdownTest"
                                >
                                    {{ selectedOptionTest || 'List of Products' }}
                                    <ChevronDownIcon class="ml-2 h-5 w-5" />
                                </button>
                                </div>

                                <transition
                                enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95"
                                >
                                <div 
                                    v-if="isOpenTest" 
                                    class="origin-top-right absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg"
                                >
                                    <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                    <a 
                                        v-for="option in optionsTest" 
                                        :key="option"
                                        href="#"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                        role="menuitem"
                                        @click.prevent="selectOptionTest(option)"
                                    >
                                        {{ option }}
                                    </a>
                                    </div>
                                </div>
                                </transition>
                        </div>



                            <div class="flex items-center">
              <div class="relative justify-center items-center inline-block text-left ">
                <!-- Settings Button -->
                <button
                  @click="toggleDropdown"
                  type="button"
                  class="inline-flex items-center justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Additional Settings
                  <ChevronDownIcon class="ml-2 h-5 w-5" />
                </button>

                <!-- Dropdown Menu -->
                <transition
                  enter-active-class="transition ease-out duration-100"
                  enter-from-class="transform opacity-0 scale-95"
                  enter-to-class="transform opacity-100 scale-100"
                  leave-active-class="transition ease-in duration-75"
                  leave-from-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95"
                >
                  <div
                    v-if="isOpen"
                    class="origin-top-right right-0 absolute mt-2 w-[265px] rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-10"
                  >
                                                <div class="px-4 flex items-center justify-center py-3">
                                                    <p class="mt-3 items-center justify-center text-md font-bold text-gray-900">Notify me about:</p>
                                                </div>
                                                <div class="py-2">
                                                    <!-- Stocks Option -->
                                                    <div class="px-4 py-2 flex justify-between items-center">
                                                        <label for="stocks" class="items-center flex text-sm text-gray-700">Stocks if it has &nbsp;</label>
                                                        <div class="flex items-center">
                                                            <input
                                                                type="number"
                                                                id="stocks"
                                                                v-model="stocksDays"
                                                                min="0"
                                                                class="w-16 text-black rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                            />
                                                            <span class="ml-2 text-sm text-gray-600">items left</span>
                                                        </div>
                                                    </div>
                                                    <!-- Expiry Date Option -->
                                                    <div class="px-4 py-2 flex justify-between items-center">
                                                        <label for="expiry" class="text-sm text-gray-700">Expiry Date if near</label>
                                                        <div class="flex items-center"> 
                                                            <input
                                                                type="number"
                                                                id="expiry"
                                                                v-model="expiryDays"
                                                                min="0"
                                                                class="w-16 text-black rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                            />
                                                            <span class="ml-2 text-sm text-gray-600">days</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Save and Cancel Buttons -->
                                                <div class="px-4 py-3 flex justify-center space-x-2">
                                                    <button @click="cancelChanges" class="transition hover:scale-105 ease-in-out duration-150 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
                                                    <button @click="saveSettings" class="transition hover:scale-105 ease-in-out duration-150 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">Save</button>
                                                </div>
                                            </div>
                                        </transition>
                                    </div>


                                <div class="flex">


                                <div class="ml-6">

                                    <div class="relative mr-6 w-96">
                                    <font-awesome-icon
                                        :icon="['fas', 'magnifying-glass']"
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-white"
                                    />
                                    <input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Search by name, category, status, or brand..."
                                        class="pl-10 pr-4 py-2 w-full bg-gray-700 text-white placeholder-gray-300 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                    </div>
                                </div>


                                </div>
                            </div>


                        </div>
                        <div class="flex-grow overflow-hidden border sm:rounded-lg border-gray-900">
                            <div class="overflow-x-auto h-full">
                                <table class="min-w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                    <tr>
                                        <th 
                                            v-if="productsTableVisibility.colIdIsVisible && isProductTableShowing" 
                                            class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left align-middle cursor-pointer whitespace-nowrap"
                                            @click="sortById">
                                            <div class="p-3 flex justify-center items-center space-x-1">
                                                <font-awesome-icon 
                                                    :icon="['fas', 'angle-down']"
                                                    :class="sortOrderId === 'asc' ? 'rotate-180' : 'rotate-0'"
                                                    class="ml-2 transition-transform duration-300 ease-in-out" 
                                                /> 
                                                <span>ID</span>
                                            </div>
                                        </th>
                                        
                                        <th v-if="productsTableVisibility.colImageIsVisible && isProductTableShowing" class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Image</th>

                                        <th 
                                            v-if="productsTableVisibility.colNameIsVisible && isProductTableShowing"
                                            class="justify-center items-center align-middle px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-center cursor-pointer"
                                            @click="sortByName">
                                            <div class="flex items-center justify-center text-center align-middle space-x-1">
                                                <font-awesome-icon 
                                                    :icon="['fas', 'angle-down']"
                                                    :class="sortOrder === 'asc' ? 'rotate-180' : 'rotate-0'"
                                                    class="ml-2 transition-transform duration-300 ease-in-out" 
                                                /> 
                                                <span>Name</span>
                                            </div>
                                        </th>
                                        <th v-if="productsTableVisibility.colBrandIsVisible && isProductTableShowing" class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left align-middle cursor-pointer whitespace-nowrap" @click="sortByBrand">
                                            <div class="flex items-center justify-center space-x-1"><font-awesome-icon :icon="['fas', 'angle-down']":class="sortOrderBrand === 'asc' ? 'rotate-180' : 'rotate-0'"class="ml-2 transition-transform duration-300 ease-in-out" /> 
                                                <span>Brand</span>
                                            </div>
                                        </th>
                                        <th  v-if="productsTableVisibility.colPriceIsVisible && isProductTableShowing" class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left align-middle cursor-pointer whitespace-nowrap" @click="sortByPrice">
                                            <div class="p-3 flex justify-center items-center space-x-1"><font-awesome-icon :icon="['fas', 'angle-down']":class="sortOrderPrice === 'asc' ? 'rotate-180' : 'rotate-0'"class="ml-2 transition-transform duration-300 ease-in-out" /> 
                                                <span>Price (PHP)</span>
                                            </div>
                                        </th>
                                        <th 
                                            v-if="productsTableVisibility.colCategoryIsVisible && isProductTableShowing" 
                                            class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left align-middle cursor-pointer whitespace-nowrap"
                                            @click="sortByCategory">
                                            <div class="p-3 flex justify-center items-center space-x-1">
                                                <font-awesome-icon 
                                                    :icon="['fas', 'angle-down']"
                                                    :class="sortOrderCategory === 'asc' ? 'rotate-180' : 'rotate-0'"
                                                    class="ml-2 transition-transform duration-300 ease-in-out" 
                                                /> 
                                                <span>Category</span>
                                            </div>
                                        </th>

                                        <th 
                                            v-if="productsTableVisibility.colStockIsVisible && isProductTableShowing" 
                                            class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left align-middle cursor-pointer whitespace-nowrap"
                                            @click="sortByStock">
                                            <div class="p-3 flex justify-center items-center space-x-1">
                                                <font-awesome-icon 
                                                    :icon="['fas', 'angle-down']"
                                                    :class="sortOrderStock === 'asc' ? 'rotate-180' : 'rotate-0'"
                                                    class="ml-2 transition-transform duration-300 ease-in-out" 
                                                /> 
                                                <span>Stock</span>
                                            </div>
                                        </th>
                                        <th v-if="productsTableVisibility.colSoldIsVisible && isProductTableShowing" class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left align-middle cursor-pointer whitespace-nowrap" @click="sortBySold">
                                            <div class="p-3 flex justify-center items-center space-x-1"><font-awesome-icon :icon="['fas', 'angle-down']":class="sortOrderSold === 'asc' ? 'rotate-180' : 'rotate-0'"class="ml-2 transition-transform duration-300 ease-in-out" /> 
                                                <span>Sold</span>
                                            </div>
                                        </th>
                                        <th v-if="productsTableVisibility.colStatusIsVisible && isProductTableShowing" class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left align-middle cursor-pointer whitespace-nowrap" @click="sortByStatus">
                                            <div class="p-3 flex justify-center items-center space-x-1"><font-awesome-icon :icon="['fas', 'angle-down']":class="sortOrderStatus === 'asc' ? 'rotate-180' : 'rotate-0'"class="ml-2 transition-transform duration-300 ease-in-out" /> 
                                                <span>Status</span>
                                            </div>
                                        </th>
                                        <th v-if="productsTableVisibility.colExpiryIsVisible && isProductTableShowing" 
                                            class=" px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left align-middle cursor-pointer whitespace-nowrap"
                                            @click="sortByExpDate">
                                            <div class="pr-3 flex justify-center items-center space-x-1">
                                                <font-awesome-icon 
                                                    :icon="['fas', 'angle-down']" 
                                                    :class="expDateSortOrder === 'asc' ? 'rotate-180' : 'rotate-0'"
                                                    class="ml-2 transition-transform duration-300 ease-in-out" 
                                                />
                                                <span>Exp. Date</span>
                                            </div>
                                        </th>
                                        <th v-if="isProductTableShowing" class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Actions</th>


                                        <!-- THIS IS FOR PRODUCT PACKAGES VISIBILITY -->
                                        <th v-if="isPackageTableShowing" class="sticky top-0 px-6 py-3 text-white bg-gray-700">Package Name</th>
                                        <th v-if="isPackageTableShowing" class="sticky top-0 px-6 py-3 text-white bg-gray-700">Products</th>
                                        <th v-if="isPackageTableShowing" class="sticky top-0 px-6 py-3 text-white bg-gray-700">Actions</th>

                                    </tr>
                                    </thead>
                                    <tbody>


                                        <tr v-for="(packageItems, packageName) in groupedPackages" :key="packageName" v-if="isPackageTableShowing" class="border-y-2 border-slate-400 text-center align-middle justify-center items-center">
                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                            {{ packageName }}
                                            </td>
                                            <td class="overflow-auto h-20 px-6 py-4 border-b text-left border-gray-200 dark:border-gray-700">
                                            <div v-for="(item, index) in packageItems" :key="item.id">
                                                
                                                <div class="flex">
                                                    <div class="flex pl-32 text-left w-1/2">
                                                        {{ item.product_name }} 
                                                    </div>
                                                    
                                                    <div class="flex items-center justify-center text-center w-1/2">
                                                        x {{ item.product_quantity }}
                                                    </div>
                                                </div>


                                                <hr v-if="index < packageItems.length - 1" class="my-2 border-gray-200 dark:border-gray-700">
                                            </div>
                                            </td>
                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                                <button @click="editPackage(packageItems[0])" class="hover:bg-yellow-600 transition hover:scale-105 ease-in-out duration-150 mr-1 bg-yellow-500 text-white px-2 py-1 rounded-full">
                                                    <font-awesome-icon icon="fa-solid fa-pen" size="sm"/>
                                                </button>
                                                <button @click="viewProductDetails(product)" class="hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 mr-1 bg-blue-500 text-white px-2 py-1 rounded-full">
                                                    <font-awesome-icon icon="fa-solid fa-eye" size="sm"/>
                                                </button>
                                                <button @click="openDeletePackageModal(packageItems[0].id)" class="hover:bg-red-600 transition hover:scale-105 ease-in-out duration-150 bg-red-500 text-white px-2 py-1 rounded-full">
                                                    <font-awesome-icon :icon="['fas', 'trash-can']" size="sm" />
                                                </button>
                                            </td>
                                        </tr>

                                    <tr v-if="filteredProducts.length === 0">
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700" colspan="12">No products available.</td>
                                    </tr>
                                    <tr v-if="isProductTableShowing" v-for="product in filteredProducts" :key="product.id">
                                        <td  v-if="productsTableVisibility.colIdIsVisible && isProductTableShowing" class="px-2 py-4 border-b border-gray-200 dark:border-gray-700 text-center">{{ product.id }}</td>
                                        <td  v-if="productsTableVisibility.colImageIsVisible && isProductTableShowing" class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center justify-center">
                                                <img :src="'/storage/' + product.image" alt="Product Image" class="w-12 h-12 object-cover"/>
                                            </div>
                                        </td>
                                        <td v-if="productsTableVisibility.colNameIsVisible && isProductTableShowing" class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ product.name }}</td>
                                        <td v-if="productsTableVisibility.colBrandIsVisible && isProductTableShowing" class="px-6 py-4 border-b border-gray-200 da   k:border-gray-700 text-center align-middle">{{ product.brand }}</td>
                                        <td v-if="productsTableVisibility.colPriceIsVisible && isProductTableShowing" class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center">{{ product.price }}</td>
                                        <td v-if="productsTableVisibility.colCategoryIsVisible && isProductTableShowing" class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center">{{ product.category }}</td>
                                        <td v-if="productsTableVisibility.colStockIsVisible && isProductTableShowing" class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center">
                                            <span v-if="product.stock === 0" 
                                                class="transition hover:scale-105 ease-in-out duration-150 hover:bg-red-700 font-semibold group inline-block rounded-full bg-red-600 text-white font-bold px-3 py-1 text-sm mr-2 cursor-pointer align-middle">
                                            !
                                            <span class="transition hover:scale-105 ease-in-out duration-150 absolute left-1/2 transform -translate-x-1/2 bottom-full mb-2 hidden group-hover:block w-max bg-gray-700 text-white text-sm rounded-md px-2 py-1">
                                                Warning, this item has no stock!
                                            </span>
                                            </span>

                                            <span v-else-if="isLowStock(product.stock)" 
                                                class="transition hover:scale-105 ease-in-out duration-150 hover:bg-yellow-600 font-semibold group inline-block rounded-full bg-yellow-500 text-white font-bold px-3 py-1 text-sm mr-2 cursor-pointer align-middle">
                                            !
                                            <span class="absolute left-1/2 transform -translate-x-1/2 bottom-full mb-2 hidden group-hover:block w-max bg-gray-700 text-white text-sm rounded-md px-2 py-1">
                                                Warning, this item is low on stock!
                                            </span>
                                            </span>
                                            

                                            <span class="inline-block align-middle">
                                            {{ product.stock }}
                                            </span>

                                        </td>
                                        <td v-if="productsTableVisibility.colSoldIsVisible && isProductTableShowing" class="px-2 py-4 border-b border-gray-200 dark:border-gray-700 text-center">{{ product.sold }}</td>
                                        <td v-if="productsTableVisibility.colStatusIsVisible && isProductTableShowing" class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center">
                                            <div class="flex items-center justify-center w-full">
                                                
                                            
                                                <span :class="{
                                                    'bg-red-500 text-white py-1 px-3 rounded-full': product.status === 'Unlisted',
                                                    'bg-green-500 text-white py-1 px-3 rounded-full': product.status === 'Listed',
                                                    'bg-gray-500 text-white py-1 px-3 rounded-full': product.status === 'Out of Stock',
                                                }">{{ product.status }}</span>
                                            </div>
                                        </td>

                                        <td v-if="productsTableVisibility.colExpiryIsVisible && isProductTableShowing"  class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">
                                            <div class="flex items-center justify-center w-full">
                                                <span v-if="isDateTodayOrPast(product.expDate)" 
                                                class="transition hover:scale-105 ease-in-out duration-150 hover:bg-red-700 font-semibold group inline-block rounded-full bg-red-600 text-white font-bold px-3 py-1 text-sm mr-2 cursor-pointer align-middle">
                                            !
                                            <span class="absolute left-1/2 transform -translate-x-1/2 bottom-full mb-2 hidden group-hover:block w-max bg-gray-700 text-white text-sm rounded-md px-2 py-1">
                                                Warning, this item has expired or is expiring today!
                                            </span>
                                            </span>
                                            
                                            <span v-else-if="isDateNear(product.expDate)" 
                                                class="transition hover:scale-105 ease-in-out duration-150 hover:bg-yellow-600 font-semibold group inline-block rounded-full bg-yellow-500 text-white font-bold px-3 py-1 text-sm mr-2 cursor-pointer align-middle">
                                            !
                                            <span class="transition hover:scale-105 ease-in-out duration-150 absolute left-1/2 transform -translate-x-1/2 bottom-full mb-2 hidden group-hover:block w-max bg-gray-700 text-white text-sm rounded-md px-2 py-1">
                                                Warning, this item is nearing its expiry date!
                                            </span>
                                            </span>
                                            <span class="inline-block align-middle">
                                            {{ formatDate(product.expDate) }}
                                            </span>
                                            </div>

                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center justify-center">
                                                <button @click="editProductDetails(product)" class="hover:bg-yellow-600 transition hover:scale-105 ease-in-out duration-150 mr-1 bg-yellow-500 text-white px-2 py-1 rounded-full">
                                                    <font-awesome-icon icon="fa-solid fa-pen" size="sm"/>
                                                </button>
                                                <button @click="openDeleteModal(product.id)" class="hover:bg-red-600 transition hover:scale-105 ease-in-out duration-150 bg-red-500 text-white px-2 py-1 rounded-full">
                                                    <font-awesome-icon :icon="['fas', 'trash-can']" size="sm" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>
                    
                </div>
                <div class="flex justify-between mt-4 mr-5 space-x-4">
                    
                    <div class="relative inline-block text-left">



                        <div>

                            <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
            >
            <div
                v-if="isOpenTable"
                class="origin-top-right absolute bottom-14 left-0 mt-2 w-[265px] rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-10"
                role="menu"
                aria-orientation="vertical"
                aria-labelledby="options-menu"
            >
                <div class="px-4 flex items-center justify-center py-3">
                    <p class="mt-3 items-center justify-center text-md font-bold text-gray-900">Visible Columns:</p>
                </div>

                <div class="pl-3 py-1" role="none">
                    <label
                    v-for="item in itemsTable"
                    :key="item.id"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 cursor-pointer"
                    role="menuitem"
                    >
                    <input
                        type="checkbox"
                        :id="item.id"
                        :checked="productsTableVisibility[item.stateKey]" 
                        @change="toggleVisibility(item)" 
                        class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                    />
                    <span class="ml-2">{{ item.label }}</span>
                    </label>
                </div>

                <div class="px-4 py-3 flex justify-center space-x-2">
                                <button @click="cancelColumnVisibilityChanges" class="transition hover:scale-105 ease-in-out duration-150 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
                                <button @click="saveColumnVisibilitySettings" class="transition hover:scale-105 ease-in-out duration-150 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">Save</button>
                </div>
            </div>
        </transition>
                            <button
                                type="button"
                                class="ml-2 inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                id="options-menu"
                                aria-haspopup="true"
                                :aria-expanded="isOpenTable"
                                @click="toggleDropdownTable"
                            >
                                Options
                                <ChevronDownIcon class="ml-2 h-5 w-5" />
                            </button>

                            

                        </div>

                        
                    </div>
                    
                    <div class="space-x-2 pl-6">
                        <button @click="showAddProductModal = true" class="hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">+ Add Product</button>
                        <button @click="showAddPackageModal = true" class="hover:bg-gray-600 transition hover:scale-105 ease-in-out duration-150 bg-gray-500 text-white py-2 px-4 rounded">Add a Package</button>
                        <button @click="showCategoriesModal = true" class="hover:bg-gray-600 transition hover:scale-105 ease-in-out duration-150 bg-gray-500 text-white py-2 px-4 rounded">Categories</button>
                        <button @click="showImportExportModal = true" class="hover:bg-gray-600 transition hover:scale-105 ease-in-out duration-150 bg-gray-500 text-white py-2 px-4 rounded">Import/Export</button>
                    </div>

                </div>



            </div>

        </div>

        <transition name="modal-fade" >
            <div v-show="showDeletePackageModal" @click="closeDeletePackageModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
                <div  @click.stop class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-trash" size="8x" style="margin-top:2px; color: red;"/>
                    <h2 class="mt-4 text-xl text-center font-bold mb-2">WARNING: Confirming Deletion</h2>
                    <p class="text-center">Are you sure you want to delete this product package?</p>
                    <p class="mb-4 text-xs text-center">Note: this product package will be permanently deleted.</p>
                    <div class="flex justify-center space-x-2">
                        <button @click="closeDeletePackageModal" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                            No
                        </button>
                        <button @click="confirmDeletePackage" class="hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">
                            Yes
                        </button>
                    </div>
                </div>
            </div>
        </transition>

        <transition name="modal-fade" >
            <div v-if="showSuccessPackageModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto h-full w-full">
                <div class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-check" size="10x" style="color: green;"/>
                    <h2 class="text-xl font-bold mb-4">Success!</h2>
                    <p class="mb-4">The Product Package has been successfully deleted.</p>
                </div>
            </div>
        </transition>
        
        <transition name="modal-fade" >
            <div v-if="showSuccessEditPackageModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto h-full w-full">
                <div class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-check" size="10x" style="color: green;"/>
                    <h2 class="text-xl font-bold mb-4">Success!</h2>
                    <p class="mb-4">The Product Package Information has been successfully Updated!.</p> 
                </div>
            </div>
        </transition>

        <transition name="modal-fade">
            <div v-if="showEditPackageModal" @click="showEditPackageModal = false" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div @click.stop class="px-5 bg-white p-4 rounded-lg shadow-lg w-full max-w-7xl relative">
                <h3 class="text-5xl font-semibold text-center mt-6 mb-5">Edit Package</h3>
                
                <div class="flex w-full justify-center items-center mb-4">
                <div class="flex w-1/3 items-center justify-center">
                    <span class="flex w-44">Package Name:</span>
                        <input
                            type="text"
                            id="packageName"
                            v-model="updatePackage.product_package_name"
                            @input="handleInputChange"
                            class="w-1/4 input-field text-xs p-1"
                        />
                </div>
                </div>

                <div class="px-12 mb-10">    
                <form @submit.prevent="updateProductPackage" class="w-full border-4 border-black rounded-bl-lg rounded-r-lg shadow-lg overflow-hidden" style="max-height: 430px;">
                    <div class="overflow-auto" style="max-height: 400px;">
                    <table class="min-w-full bg-white border-gray-700">
                        <thead class="border-b rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="sticky top-0 z-20 px-6 py-3 text-white bg-gray-700">Product</th>
                            <th class="sticky top-0 z-20 px-6 py-3 text-white bg-gray-700">Amount</th>
                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Quantity</th>
                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Stock</th>
                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(field, index) in selectedInvoiceItems" :key="index" :class="index % 2 === 0 ? 'bg-blue-900 bg-opacity-5' : 'bg-white'" class="items-center text-center">
                            
                            <td class="px-6 py-3 border-b border-gray-200 dark:border-gray-400 align-middle">
                                <div class="flex items-center justify-center">
                                    <div class="flex flex-shrink-0 items-center">
                                        <div v-if="field.image">
                                            <img :src="'/storage/' + field.image" class="w-5 h-5 object-cover" />
                                        </div>
                                        <div v-else>
                                            <font-awesome-icon :icon="['fas', 'image']" class="w-5 h-5 object-cover" size="sm" />
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <input  class="w-44" @input="field.isSearching = true" type="text" v-model="field.product_name" placeholder="Search for a Product" />
                                    </div>

                                    <div class="relative z-10 ">
                                        <!-- Assuming you have an input field above this list -->
                                        <ul
                                        v-if="field.product_name && field.isSearching"
                                        class="ml-10 w-80 bg-white shadow-xl rounded-lg mt-2 max-h-80 overflow-y-auto border border-gray-200"
                                        >
                                        <li
                                            v-for="product in filteredUpdateProducts(field.product_name)"
                                            :key="product.id"
                                            @click="selectUpdateProduct(product, index)"
                                            class="flex items-center p-3 hover:bg-gray-50 transition-colors duration-150 ease-in-out cursor-pointer"
                                        >
                                            <div class="flex-shrink-0">
                                            <img
                                                :src="'/storage/' + product.image"
                                                :alt="product.name"
                                                class="w-12 h-12 object-cover rounded-md"
                                            />
                                            </div>
                                            <div class="ml-4 flex-grow">
                                            <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
                                            <p class="text-sm text-gray-500">{{ product.price }}</p>
                                            </div>
                                        </li>
                                        </ul>
                                    </div>

                                </div>
                                
                            </td>

                            <td class="pr-8 py-4 border-b border-gray-200 dark:border-gray-400">
                            <div class="z-10 flex items-center justify-center">
                                <div class="z-10 -ml-4 flex items-center justify-between">
                                <span class="z-10 -ml-20 w-24 text-right text-xs text-gray-400">
                                    {{ field.on_sale === 'yes' ? 'On' : 'Not' }}<br>
                                    {{ field.on_sale === 'yes' ? 'Sale' : 'On Sale' }}
                                </span>
                                <label class="z-10 switch px-3">
                                    <input class="z-10" type="checkbox" v-model="field.on_sale" true-value="yes" false-value="no" />
                                    <span class="z-10 slider round"></span>
                                </label>
                                </div>
                                <input class="no-spinner w-32" type="number" v-model="field.amount" placeholder="Amount" />
                            </div>
                            </td>

                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                            <input @keypress="validateKeyPress" class="text-center no-spinner w-16" type="number" @input="updateTotalProductAmountUpdate(index)" v-model="field.product_quantity" placeholder="Qty." />
                            </td>

                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                            <input class="text-center no-spinner w-16" type="number" v-model="field.stock" placeholder="Stock." />
                            </td>

                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                            <button type="button" class="bg-red-500 text-white p-3 rounded-full" @click="removeItemTextField1(index)">
                                <FontAwesomeIcon icon="fa-solid fa-trash-can" />
                            </button>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="6">
                            <div class="flex items-center justify-center my-6">
                                <button type="button" @click="addUpdateItemTextField" class="flex items-center justify-center">
                                <FontAwesomeIcon :icon="['fas', 'plus']" class="w-6 h-6 mr-2" />
                                Click to add New Item
                                </button>
                            </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                </form>
                </div>
                <div class="flex justify-center mt-4 pb-6 gap-4">
                <button @click="showEditPackageModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 hover:scale-105 duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Cancel</button>
                <button @click.prevent="updateProductPackage" class="hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">Update Package</button>
                </div>
            </div>
            </div>
        </transition>

        <transition name="modal-fade" >
            <div v-if="showAddPackageModal" @click="showAddPackageModal = false" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
                    <div @click.stop class="px-5 bg-white p-4 rounded-lg shadow-lg w-full max-w-7xl relative">
                    <h3 class="text-5xl font-semibold text-center mt-6 mb-5">Add a Package</h3>
                            
                        <div class="flex w-full justify-center items-center mb-4">
                            <div class="flex w-1/3 items-center justify-center">
                                <span class="flex w-44">Package Name:</span> <input type="text" id="brand" v-model="newPackage.product_package_name" class="w-1/4 input-field text-xs p-1"/>
                            </div>
                        </div>


                    <div class="px-12 mb-10">    
                        <form @submit.prevent="addProductPackage" class="w-full border-4 border-black rounded-bl-lg rounded-r-lg shadow-lg overflow-hidden" style="max-height: 430px;">
                            <div class="overflow-auto" style="max-height: 400px;">
                            <table class="min-w-full bg-white border-gray-700">
                            <thead class="border-b rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                                <tr>
                                <th class="sticky top-0 z-20 px-6 py-3 text-white bg-gray-700">Product</th>
                                <th class="sticky top-0 z-20 px-6 py-3 text-white bg-gray-700">Amount</th>
                                <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Quantity</th>
                                <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Stock</th>
                                <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Total Amount</th>
                                <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Actions</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(field, index) in textItemFields" :key="index" :class="index % 2 === 0 ? 'bg-blue-900 bg-opacity-5' : 'bg-white'" class="items-center text-center">
                                <td class="px-6 py-3 border-b border-gray-200 dark:border-gray-400 align-middle">
                                    <div class="flex items-center justify-center relative">
                                    <div class="flex flex-shrink-0 items-center">
                                        <div v-if="field.image">
                                        <img :src="'/storage/' + field.image" class="w-5 h-5 object-cover" alt="Product image" />
                                        </div>      
                                        <div v-else>
                                        <FontAwesomeIcon :icon="['fas', 'image']" class="w-5 h-5 object-cover" />
                                        </div>
                                    </div>
                                    <div class="ml-4 relative w-full">
                                        <input 
                                        class="w-44" 
                                        @input="field.isSearching = true" 
                                        type="text" 
                                        v-model="field.searchProductQuery" 
                                        placeholder="Search for a Product" 
                                        />
                                        
                                        <ul
                                        v-if="field.searchProductQuery && field.isSearching"
                                        class="absolute left-0 mt-1 w-full bg-white shadow-xl rounded-lg max-h-80 overflow-y-auto border border-gray-200 z-50"
                                        >
                                        <li
                                            v-for="product in filteredProductsForPackage(field.searchProductQuery)"
                                            :key="product.id"
                                            @click="selectProduct(product, index)"
                                            class="flex items-center p-3 hover:bg-gray-50 transition-colors duration-150 ease-in-out cursor-pointer"
                                        >
                                            <div class="flex-shrink-0">
                                            <img
                                                :src="'/storage/' + product.image"
                                                :alt="product.name"
                                                class="w-12 h-12 object-cover rounded-md"
                                            />
                                            </div>
                                            <div class="ml-4 flex-grow">
                                            <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
                                            <p class="text-sm text-gray-500">{{ product.price }}</p>
                                            </div>
                                        </li>
                                        </ul>
                                    </div>
                                    </div>
                                </td>

                                <td class="pr-8 py-4 border-b border-gray-200 dark:border-gray-400">
                                    <div class="z-10 flex items-center justify-center">
                                    <div class="z-10 -ml-4 flex items-center justify-between">
                                        <span class="z-10 -ml-20 w-24 text-right text-xs text-gray-400">
                                        {{ field.on_sale === 'yes' ? 'On' : 'Not' }}<br>
                                        {{ field.on_sale === 'yes' ? 'Sale' : 'On Sale' }}
                                        </span>
                                        <label class="z-10 switch px-3">
                                        <input class="z-10" disabled :disabled="!field.areFieldsEnabled" type="checkbox" v-model="field.on_sale" true-value="yes" false-value="no" />
                                        <span class="z-10 slider round"></span>
                                        </label>
                                    </div>
                                    <input disabled class="no-spinner w-32" type="number" v-model="field.amount" placeholder="Amount" />
                                    </div>
                                </td>

                                <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                    <input @keypress="validateKeyPress" :disabled="!field.areFieldsEnabled" class="text-center no-spinner w-16" type="number" @input="updateTotalProductAmount(index)" v-model="field.quantity" placeholder="Qty." />
                                </td>

                                <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                    <input disabled :disabled="!field.areFieldsEnabled" class="text-center no-spinner w-16" type="number" v-model="field.stock" placeholder="Stock." />
                                </td>

                                <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                    <input @keypress="validateKeyPress" :disabled="!field.areFieldsEnabled" class="text-center no-spinner w-32" type="number" v-model="field.total_amount" placeholder="Total Amount" />
                                </td>

                                <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                    <button type="button" class="bg-red-500 text-white p-3 rounded-full" @click="removeItemTextField(index)">
                                    <FontAwesomeIcon icon="fa-solid fa-trash-can" />
                                    </button>
                                </td>
                                </tr>

                                <tr>
                                <td colspan="6">
                                    <div class="flex items-center justify-center my-6">
                                    <button type="button" @click="addItemTextField" class="flex items-center justify-center">
                                        <FontAwesomeIcon :icon="['fas', 'plus']" class="w-6 h-6 mr-2" />
                                        Click to add New Field
                                    </button>
                                    </div>
                                </td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                        </form>

                    </div>
                    <div class="flex justify-center mt-4 pb-6 gap-4">
                            <button @click="showAddPackageModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 hover:scale-105 duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Cancel</button>
                            <button @click.prevent="addProductPackage()" class="hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">Save Package</button>
                    </div>
                </div>
            </div>
        </transition>

        <transition name="modal-fade" >
            <div v-show="showDeleteModal" @click="closeDeleteModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
                <div  @click.stop class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-trash" size="8x" style="margin-top:2px; color: red;"/>
                    <h2 class="mt-4 text-xl text-center font-bold mb-2">WARNING: Confirming Deletion</h2>
                    <p class="text-center">Are you sure you want to delete this invoice?</p>
                    <p class="mb-4 text-xs text-center">Note: this invoice will be permanently deleted.</p>
                    <div class="flex justify-center space-x-2">
                        <button @click="closeDeleteModal" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                            No
                        </button>
                        <button @click="confirmDelete" class="hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">
                            Yes
                        </button>
                    </div>
                </div>
            </div>
        </transition>

        <transition name="modal-fade" >
            <div v-if="showSuccessModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto h-full w-full">
                <div class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-check" size="10x" style="color: green;"/>
                    <h2 class="text-xl font-bold mb-4">Success!</h2>
                    <p class="mb-4">The Product has been successfully deleted.</p>
                </div>
            </div>
        </transition>


        <transition name="modal-fade" >
            <div v-if="showSuccessEditModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto h-full w-full">
                <div class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-check" size="10x" style="color: green;"/>
                    <h2 class="text-xl font-bold mb-4">Success!</h2>
                    <p class="mb-4">The Product Information has been successfully Updated!.</p> 
                </div>
            </div>
        </transition>

        <transition name="modal-fade" >
            <div v-show="showDeleteModal" @click="closeDeleteModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
                <div  @click.stop class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-trash" size="8x" style="margin-top:2px; color: red;"/>
                    <h2 class="mt-4 text-xl text-center font-bold mb-2">WARNING: Confirming Deletion</h2>
                    <p class="text-center">Are you sure you want to delete this product?</p>
                    <p class="mb-4 text-xs text-center">Note: this product will be permanently deleted.</p>
                    <div class="flex justify-center space-x-2">
                        <button @click="closeDeleteModal" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                            No
                        </button>
                        <button @click="confirmDelete" class="hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">
                            Yes
                        </button>
                    </div>
                </div>
            </div>
        </transition>



        <!-- <transition name="toast">
            <div v-if="showSuccessAddModal" class="fixed top-4 right-4 flex items-center p-4 mb-4 w-full max-w-xs text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800 z-50" role="alert">
            <div class="inline-flex flex-shrink-0 justify-center items-center w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
                <CheckCircle class="w-5 h-5" />
            </div>
            <div class="ml-3 text-sm font-normal">The Product Information has been successfully Added!</div>
            <button @click="dismiss" type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" aria-label="Close">
                <span class="sr-only">Close</span>
                <X class="w-5 h-5" />
            </button>
            </div>
        </transition> -->
        <transition name="modal-fade" >
            <div v-if="showSuccessNotifModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto h-full w-full">
                <div class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-check" size="10x" style="color: green;"/>
                    <h2 class="text-xl font-bold mb-4">Success!</h2>
                    <p class="mb-4">The Product Notifications has been successfully Saved!.</p>
                </div>
            </div>
        </transition>

        <transition name="modal-fade" >
            <div v-if="showSuccessAddModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto h-full w-full">
                <div class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-check" size="10x" style="color: green;"/>
                    <h2 class="text-xl font-bold mb-4">Success!</h2>
                    <p class="mb-4">The Product Information has been successfully Added!.</p>
                </div>
            </div>
        </transition>

        <transition name="modal-fade" >
            <div v-if="showImportExportModal" @click="showImportExportModal = false" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
                <div  @click.stop class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 relative">
                <h2 class="text-2xl font-bold text-center mb-6">Import/Export Products</h2>
                
                <button 
                    @click="closeModal" 
                    class="absolute top-2 right-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    aria-label="Close"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>


                <div class="flex space-x-6">
                    <!-- Import section -->
                    <div class="w-1/2 border-r pr-6">
                    <h3 class="text-lg font-semibold mb-4">Import Products from Excel</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span></p>
                            <p class="text-xs text-gray-500">XLSX, (MAX. 10MB)</p>
                            </div>
                            <input id="dropzone-file" type="file" class="hidden" ref="file" @change="handleFileImportUpload" accept=".xlsx, .xls, .csv" />
                            <i v-if="showFileInput" class="fa-solid fa-file text-[30px]"></i>
                        </label>
                        </div>
                        <button @click="uploadFileImport" class="text-sm hover:bg-blue-600 w-full transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">
                        Upload File
                        </button>
                    </div>
                    </div>

                    <!-- Export section -->
                    <div class="w-1/2 pl-6">
                    <h3 class="text-lg font-semibold mb-4">Export Products</h3>
                    <div class="space-y-4">
                        <div>
                        <label for="exportFormat" class="block text-sm font-medium text-gray-700">Export Format</label>
                        <select 
                            id="exportFormat" 
                            v-model="summaryOption.option"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                        >
                            <option value="summaryExcel">Excel Sheet (.xlsx)</option>
                        </select>
                        </div>
                        <button @click="printInventorySummary()" class="text-sm hover:bg-blue-600 w-full transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">
                        Export Products
                        </button>
                    </div>
                    <p class="text-center text-gray-500 justify-center">or</p>
                    <div class="flex items-center justify-center">
                    <button @click="printInventoryTemplate()" class="justify-center text-sm hover:bg-blue-600 w-1/2 transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">
                        Download Template
                    </button>
                    </div>



                    </div>
                </div>

                <div v-if="message" class="mt-4 p-4 rounded-md" :class="messageType === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'">
                    {{ message }}
                </div>
                </div>
            </div>
        </transition>
        <!-- For Adding of products -->
        <transition name="modal-fade" >
            <div v-if="showAddProductModal" @click="showAddProductModal = false" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
                <div  @click.stop class="px-5 bg-white p-4 rounded-lg shadow-lg w-full max-w-3xl relative">
                    <h3 class="text-5xl font-semibold text-center mt-6 mb-5">Add a Product</h3>
                    <form @submit.prevent="addProduct">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-1">
                                <label style="font-size: 10px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block  text-white">
                                    Image (file type: jpg, jpeg, png) <span class="text-red-500">*</span>
                                    <span class="text-xs text-gray-500 block">Maximum file size is 5MB.</span>
                                </label>
                                <div class="image-upload relative w-full h-40 border border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-400 cursor-pointer">
                                    <input type="file" id="image" @change="handleImageUpload" class="absolute inset-0 opacity-0 cursor-pointer" />
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <img v-if="imagePreviewUrl" :src="imagePreviewUrl" class="absolute inset-0 w-full h-full object-cover" />
                                </div>
                            </div>
                            <div class="col-span-2 grid grid-cols-2 gap-3">
                                <!-- Name Field -->
                                <div class="col-span-2">
                                    <label for="name" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block  text-white">Name <span class="text-red-500">*</span> <span class="text-gray-500 text-xs">(max 64 characters only)</span></label>
                                    <input maxlength="64" type="text" id="name" v-model="newProduct.name" class="input-field w-full text-xs p-1" required />
                                    <span v-if="validationErrors.name" class="text-red-500 text-xs">{{ validationErrors.name }}</span>
                                </div>
                                <!-- Price Field -->
                                <div>
                                    <label for="price" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline  text-white">Price (PHP) <span class="text-red-500">*</span></label>
                                    <input type="number" min="0"  step="0.01" id="price" v-model="newProduct.price" class="input-field text-xs p-1" required @input="validatePrice" />
                                    <div v-if="priceError" class="text-red-500 text-sm">{{ priceError }}</div>
                                    <span v-if="validationErrors.price" class="text-red-500 text-xs">{{ validationErrors.price }}</span>
                                </div>
                                <!-- Category Field -->
                                <div>
                                    <label for="category" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline  text-white">Category <span class="text-red-500">*</span></label>
                                    <select id="category" v-model="newProduct.category" class="input-field text-xs p-1" required>
                                        <option v-for="category in listedCategories" :key="category.id" :value="category.name">{{ category.name }}</option>
                                    </select>
                                    <span v-if="validationErrors.category" class="text-red-500 text-xs">{{ validationErrors.category }}</span>
                                </div>
                                <!-- Stock Field -->
                                <div>
                                    <label for="stock" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline  text-white">Stock <span class="text-red-500">*</span></label>
                                    <input type="number" min="0"  id="stock" v-model="newProduct.stock" class="input-field text-xs p-1" required @input="validateStock" />
                                    <div v-if="stockError" class="text-red-500 text-sm">{{ stockError }}</div>
                                </div>

                                <!-- Sold Field -->
                                <div>
                                    <label for="sold" style="font-size: 11px;" class=" pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline   text-white">Items Already Sold  <span class="text-red-500">*</span></label>
                                    <input type="number" min="0"  id="sold" v-model="newProduct.sold" class="input-field text-xs p-1" @input="validateSold" />
                                    <div v-if="soldError" class="text-red-500 text-sm">{{ soldError }}</div>
                                </div>
                                <!-- Status Field -->
                                <div>
                                    <label for="status" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline  text-white">Status <span class="text-red-500">*</span></label>
                                    <select id="status" v-model="newProduct.status" class="input-field text-xs p-1" required>
                                        <option value="Listed">Listed</option>
                                        <option value="Unlisted">Unlisted</option>
                                        <option value="Out of Stock">Out of Stock</option>
                                    </select>
                                    <span v-if="validationErrors.status" class="text-red-500 text-xs">{{ validationErrors.status }}</span>
                                </div>
                                <!-- Brand Field -->
                                <div>
                                    <label for="brand" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline  text-white">Brand <span class="text-red-500">*</span></label>
                                    <input type="text" id="brand" v-model="newProduct.brand" class="input-field text-xs p-1"/>
                                    <span v-if="validationErrors.brand" class="text-red-500 text-xs">{{ validationErrors.brand }}</span>
                                </div>
                            </div>
                            <!-- Description -->
                            <div class="col-span-3">
                                <label for="description" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block  text-white"> Description: <span class="text-red-500">*</span></label>
                                <textarea id="description" v-model="newProduct.description" rows="2" class="input-field text-xs p-1" placeholder="Enter your description here (will be shown on the website)…"></textarea>
                                <span v-if="validationErrors.description" class="text-red-500 text-xs">{{ validationErrors.description }}</span>
                            </div>
                        <div class="col-span-2 grid grid-cols-3 gap-3">
                            <!-- Expiry Date -->
                            <div>
                                <label for="expDate" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline  text-white">Expiry Date <span class="text-red-500">*</span></label>
                                <input type="date" id="expDate" v-model="newProduct.expDate" class="input-field text-xs p-1"/>
                                <span v-if="validationErrors.expDate" class="text-red-500 text-xs">{{ validationErrors.expDate }}</span>
                            </div>
                            <!-- Featured Toggle -->
                            <div class="flex flex-col space-y-2 ml-8">
                                <label class="flex items-center text-xs font-medium text-gray-700 whitespace-nowrap">
                                    Featured:
                                    <span class="relative group ml-1">
                                        <font-awesome-icon icon="fa-solid fa-circle-info" class="text-gray-500 cursor-pointer" />
                                        <!-- Tooltip -->
                                        <span class="absolute left-full top-1/2 transform -translate-y-1/2 ml-4 bg-gray-800 text-white text-xs rounded-md px-3 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300 whitespace-nowrap z-10 pointer-events-none">
                                            Enable this to feature the product on the website.
                                        </span>
                                    </span>
                                </label>
                                <div class="flex items-center space-x-2">
                                    <label class="switch">
                                        <input type="checkbox" v-model="newProduct.featured" true-value="true" false-value="false"/>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="text-xs text-gray-700">{{ newProduct.featured === 'true' ? 'True' : 'False' }}</span>
                                </div>
                            </div>
                            <!-- On Sale Toggle -->
                            <div class="col-span-2">
                                <label class="flex items-center text-xs font-medium text-gray-700">
                                    On Sale:
                                    <span class="relative group ml-1">
                                        <font-awesome-icon icon="fa-solid fa-circle-info" class="text-gray-500 cursor-pointer" />
                                        <!-- Tooltip -->
                                        <span class="absolute left-full top-1/2 transform -translate-y-1/2 ml-4 bg-gray-800 text-white text-xs rounded-md px-3 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300 whitespace-nowrap z-10 pointer-events-none">
                                            Enable this to make the product on sale.
                                        </span>
                                    </span>
                                </label>
                                <div class="flex items-center space-x-2">
                                    <label class="switch">
                                        <input type="checkbox" v-model="newProduct.on_sale" true-value="yes" false-value="no"/>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="text-xs text-gray-700">{{ newProduct.on_sale === 'yes' ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>
                        </div>
                            <!-- On Sale Price (Visible only if 'On Sale' is selected) -->
                            <div class="col-span-3" v-if="newProduct.on_sale === 'yes'">
                                <label for="on_sale_price" class="block text-xs font-medium text-gray-700">On Sale Price (PHP):</label>
                                <input type="number" min="0"  step="0.01" id="on_sale_price" v-model="newProduct.on_sale_price" class="input-field text-xs p-1" @input="validateOnsaleError"/>
                                <div v-if="onsaleError" class="text-red-500 text-sm">{{ onsaleError }}</div>
                            </div>
                            <!-- Action Buttons -->
                            <div class="col-span-3 flex justify-center mt-3 space-x-2">
                                <button @click="cancelAddProduct" class="px-4 py-2 block text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 hover:scale-105 duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Cancel</button>
                                <button type="submit" class="hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">Save Product</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </transition>

        <transition name="modal-fade" >
        <div v-if="showEditProductModal" @click="showEditProductModal = false" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div  @click.stop class="px-5 bg-white p-4 rounded-lg shadow-lg w-full max-w-3xl relative">
                <h3 class="text-5xl font-semibold text-center mt-6 mb-5">Edit Product</h3>
                <form @submit.prevent="updateProduct">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label style="font-size: 10px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block  text-white">
                                Image (file type: jpg, jpeg, png) <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500 block">Maximum file size is 5MB.</span>
                            </label>
                            <div class="image-upload relative w-full h-40 border border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-400 cursor-pointer">
                                <input type="file" id="edit_image" @change="handleEditImageUpload" class="absolute inset-0 opacity-0 cursor-pointer" />
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-if="!editImagePreviewUrl">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <img v-if="editImagePreviewUrl" :src="editImagePreviewUrl" class="absolute inset-0 w-full h-full object-cover" />
                            </div>
                        </div>
                        <div class="col-span-2 grid grid-cols-2 gap-3">
                            <!-- Name Field -->
                            <div class="col-span-2">
                                <label for="edit_name" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-white">Name <span class="text-red-500">*</span></label>
                                <input type="text" id="edit_name" v-model="editProduct.name" class="input-field w-full text-xs p-1" required />
                                <span v-if="validationErrorsEdit.name" class="text-red-500 text-xs">{{ validationErrorsEdit.name }}</span>
                            </div>
                            <!-- Price Field -->
                            <div>
                                <label for="edit_price" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline text-white">Price (PHP) <span class="text-red-500">*</span></label>
                                <input type="number" min="0" step="0.01" id="edit_price" v-model="editProduct.price" class="input-field text-xs p-1" required @input="validateUpdatePrice" />
                                <div v-if="UpdatepriceError" class="text-red-500 text-sm">{{ UpdatepriceError }}</div>
                                <span v-if="validationErrorsEdit.price" class="text-red-500 text-xs">{{ validationErrorsEdit.price }}</span>
                            </div>
                            <!-- Category Field -->
                            <div>
                                <label for="edit_category" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline text-white">Category <span class="text-red-500">*</span></label>
                                <select id="edit_category" v-model="editProduct.category" class="input-field text-xs p-1" required>
                                    <option v-for="category in listedCategories" :key="category.id" :value="category.name">{{ category.name }}</option>
                                </select>
                                <span v-if="validationErrorsEdit.category" class="text-red-500 text-xs">{{ validationErrorsEdit.category }}</span>
                            </div>
                            <!-- Stock Field -->
                            <div>
                                <label for="edit_stock" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline text-white">Stock <span class="text-red-500">*</span></label>
                                <input type="number" min="0" id="edit_stock" v-model="editProduct.stock" class="input-field text-xs p-1" required @input="validateUpdateStock" />
                                <div v-if="UpdatestockError" class="text-red-500 text-sm">{{ UpdatestockError }}</div>
                            </div>

                            <!-- Sold Field -->
                            <div>
                                <label for="edit_sold" style="font-size: 11px;" class=" pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline   text-white">Sold  <span class="text-red-500">*</span></label>
                                <input type="number" min="0" id="edit_sold" v-model="editProduct.sold" class="input-field text-xs p-1" @input="validateUpdateSold" />
                                <div v-if="UpdatesoldError" class="text-red-500 text-sm">{{ UpdatesoldError }}</div>
                            </div>
                            <!-- Status Field -->
                            <div>
                                <label for="edit_status" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline text-white">Status <span class="text-red-500">*</span></label>
                                <select id="edit_status" v-model="editProduct.status" class="input-field text-xs p-1" required>
                                    <option value="Listed">Listed</option>
                                    <option value="Unlisted">Unlisted</option>
                                    <option value="Out of Stock">Out of Stock</option>
                                </select>
                                <span v-if="validationErrorsEdit.status" class="text-red-500 text-xs">{{ validationErrorsEdit.status }}</span>
                            </div>
                            <!-- Brand Field -->
                            <div>
                                <label for="edit_brand" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline text-white">Brand <span class="text-red-500">*</span></label>
                                <input type="text" id="edit_brand" v-model="editProduct.brand" class="input-field text-xs p-1"/>
                                <span v-if="validationErrorsEdit.brand" class="text-red-500 text-xs">{{ validationErrorsEdit.brand }}</span>
                            </div>
                        </div>
                        <!-- Description -->
                        <div class="col-span-3">
                            <label for="edit_description" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-white">Description: <span class="text-red-500">*</span></label>
                            <textarea id="edit_description" v-model="editProduct.description" rows="2" class="input-field text-xs p-1" placeholder="Enter your description here (will be shown on the website)…"></textarea>
                            <span v-if="validationErrorsEdit.description" class="text-red-500 text-xs">{{ validationErrorsEdit.description }}</span>
                        </div>
                        <!-- Expiry Date, Discountable, and Featured -->
                        <div class="col-span-2 grid grid-cols-3 gap-3">
                            <div>
                                <label for="edit_expDate" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-white">Expiry Date <span class="text-red-500">*</span></label>
                                <input type="date" id="edit_expDate" v-model="editProduct.expDate" class="input-field text-xs p-1"/>
                                <span v-if="validationErrorsEdit.expDate" class="text-red-500 text-xs">{{ validationErrorsEdit.expDate }}</span>
                            </div>
                            <!-- Featured Toggle -->
                            <div>
                                <label style="font-size: 11px;" class="block text-xs font-medium text-gray-700">Featured:
                                    <span class="relative group ml-1">
                                        <font-awesome-icon icon="fa-solid fa-circle-info" class="text-gray-500 cursor-pointer" />
                                        <span class="absolute left-full top-1/2 transform -translate-y-1/2 ml-4 bg-gray-800 text-white text-xs rounded-md px-3 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300 whitespace-nowrap z-10 pointer-events-none">
                                            Enable this to feature the product on the website.
                                        </span>
                                    </span>
                                </label>
                                <div class="flex items-center space-x-2">
                                    <label class="switch">
                                        <input type="checkbox" v-model="editProduct.featured" true-value="true" false-value="false"/>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="text-xs text-gray-700">{{ editProduct.featured === 'true' ? 'True' : 'False' }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- On Sale Toggle (Separate Row) -->
                        <div class="col-span-2">
                            <label style="font-size: 11px;" class="block text-xs font-medium text-gray-700">On Sale:
                                <span class="relative group ml-1">
                                    <font-awesome-icon icon="fa-solid fa-circle-info" class="text-gray-500 cursor-pointer" />
                                    <span class="absolute left-full top-1/2 transform -translate-y-1/2 ml-4 bg-gray-800 text-white text-xs rounded-md px-3 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300 whitespace-nowrap z-10 pointer-events-none">
                                        Enable this to apply sale price.
                                    </span>
                                </span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <label class="switch">
                                    <input type="checkbox" v-model="editProduct.on_sale" true-value="yes" false-value="no" />
                                    <span class="slider round"></span>
                                </label>
                                <span class="text-xs text-gray-700">{{ editProduct.on_sale === 'yes' ? 'Yes' : 'No' }}</span>
                            </div>
                            <input step="0.01" v-if="editProduct.on_sale === 'yes'" type="number" min="0" id="edit_on_sale_price" v-model="editProduct.on_sale_price" class="input-field mt-2 text-xs p-1" placeholder="On Sale Price (PHP)"  @input="validateUpdateOnsaleError"/>
                            <div v-if="UpdateonsaleError" class="text-red-500 text-sm">{{ UpdateonsaleError }}</div>
                        </div>
                    </div>
                    <div class="col-span-3 flex justify-center mt-3 space-x-2">
                        <button @click="showEditProductModal=false" class="px-4 py-2 block text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 hover:scale-105 duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Cancel</button>
                        <button type="submit" class="hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">Update Product</button>


                    </div>
                </form>
            </div>
        </div>
        </transition>

        <transition name="modal-fade" >
        <div v-if="showViewProductModal" @click="showViewProductModal = false" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div  @click.stop class="px-5 bg-white p-4 rounded-lg shadow-lg w-full max-w-3xl relative">
                <h3 class="text-5xl font-semibold text-center mt-6 mb-5">View Product</h3>
                <form @submit.prevent="">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label style="font-size: 10px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block  text-white">
                                Image (file type: jpg, jpeg, png) <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500 block">Maximum file size is 5MB.</span>
                            </label>
                            <div class="image-upload relative w-full h-40 border border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-400 cursor-pointer">
                                <input disabled type="file" id="edit_image" @change="handleEditImageUpload" class="absolute inset-0 opacity-0 cursor-pointer" />
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-if="!editImagePreviewUrl">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <img v-if="editImagePreviewUrl" :src="editImagePreviewUrl" class="absolute inset-0 w-full h-full object-cover" />
                            </div>
                        </div>
                        <div class="col-span-2 grid grid-cols-2 gap-3">
                            <!-- Name Field -->
                            <div class="col-span-2">
                                <label for="edit_name" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-white">Name <span class="text-red-500">*</span></label>
                                <input disabled type="text" id="edit_name" v-model="editProduct.name" class="input-field w-full text-xs p-1" required />
                                <span v-if="validationErrorsEdit.name" class="text-red-500 text-xs">{{ validationErrorsEdit.name }}</span>
                            </div>
                            <!-- Price Field -->
                            <div>
                                <label for="edit_price" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline text-white">Price (PHP) <span class="text-red-500">*</span></label>
                                <input disabled type="number" id="edit_price" v-model="editProduct.price" class="input-field text-xs p-1" required />
                                <span v-if="validationErrorsEdit.price" class="text-red-500 text-xs">{{ validationErrorsEdit.price }}</span>
                            </div>
                            <!-- Category Field -->
                            <div>
                                <label for="edit_category" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline text-white">Category <span class="text-red-500">*</span></label>
                                <select disabled id="edit_category" v-model="editProduct.category" class="input-field text-xs p-1" required>
                                    <option v-for="category in listedCategories" :key="category.id" :value="category.name">{{ category.name }}</option>
                                </select>
                                <span v-if="validationErrorsEdit.category" class="text-red-500 text-xs">{{ validationErrorsEdit.category }}</span>
                            </div>
                            <!-- Stock Field -->
                            <div>
                                <label for="edit_stock" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline text-white">Stock <span class="text-red-500">*</span></label>
                                <input disabled type="number" id="edit_stock" v-model="editProduct.stock" class="input-field text-xs p-1" required />
                                <span v-if="validationErrorsEdit.stock" class="text-red-500 text-xs">{{ validationErrorsEdit.stock }}</span>
                            </div>

                            <!-- Sold Field -->
                            <div>
                                <label for="edit_sold" style="font-size: 11px;" class=" pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline   text-white">Sold  <span class="text-red-500">*</span></label>
                                <input disabled type="number" id="edit_sold" v-model="editProduct.sold" class="input-field text-xs p-1" />
                            </div>
                            <!-- Status Field -->
                            <div>
                                <label for="edit_status" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline text-white">Status <span class="text-red-500">*</span></label>
                                <select disabled id="edit_status" v-model="editProduct.status" class="input-field text-xs p-1" required>
                                    <option value="Listed">Listed</option>
                                    <option value="Unlisted">Unlisted</option>
                                    <option value="Out of Stock">Out of Stock</option>
                                </select>
                                <span v-if="validationErrorsEdit.status" class="text-red-500 text-xs">{{ validationErrorsEdit.status }}</span>
                            </div>
                            <!-- Brand Field -->
                            <div>
                                <label for="edit_brand" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline text-white">Brand <span class="text-red-500">*</span></label>
                                <input disabled type="text" id="edit_brand" v-model="editProduct.brand" class="input-field text-xs p-1"/>
                                <span v-if="validationErrorsEdit.brand" class="text-red-500 text-xs">{{ validationErrorsEdit.brand }}</span>
                            </div>
                        </div>
                        <!-- Description -->
                        <div class="col-span-3">
                            <label for="edit_description" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-white">Description: <span class="text-red-500">*</span></label>
                            <textarea id="edit_description" v-model="editProduct.description" rows="2" class="input-field text-xs p-1" placeholder="Enter your description here (will be shown on the website)…"></textarea>
                            <span v-if="validationErrorsEdit.description" class="text-red-500 text-xs">{{ validationErrorsEdit.description }}</span>
                        </div>
                        <!-- Expiry Date, Discountable, and Featured -->
                        <div class="col-span-2 grid grid-cols-3 gap-3">
                            <div>
                                <label for="edit_expDate" style="font-size: 11px;" class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-white">Expiry Date <span class="text-red-500">*</span></label>
                                <input disabled type="date" id="edit_expDate" v-model="editProduct.expDate" class="input-field text-xs p-1"/>
                                <span v-if="validationErrorsEdit.expDate" class="text-red-500 text-xs">{{ validationErrorsEdit.expDate }}</span>
                            </div>
                            <!-- Featured Toggle -->
                            <div>
                                <label style="font-size: 11px;" class="block text-xs font-medium text-gray-700">Featured:
                                    <span class="relative group ml-1">
                                        <font-awesome-icon icon="fa-solid fa-circle-info" class="text-gray-500 cursor-pointer" />
                                        <span class="absolute left-full top-1/2 transform -translate-y-1/2 ml-4 bg-gray-800 text-white text-xs rounded-md px-3 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300 whitespace-nowrap z-10 pointer-events-none">
                                            Enable this to feature the product on the website.
                                        </span>
                                    </span>
                                </label>
                                <div class="flex items-center space-x-2">
                                    <label class="switch">
                                        <input disabled type="checkbox" v-model="editProduct.featured" true-value="true" false-value="false"/>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="text-xs text-gray-700">{{ editProduct.featured === 'true' ? 'True' : 'False' }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- On Sale Toggle (Separate Row) -->
                        <div class="col-span-2">
                            <label style="font-size: 11px;" class="block text-xs font-medium text-gray-700">On Sale:
                                <span class="relative group ml-1">
                                    <font-awesome-icon icon="fa-solid fa-circle-info" class="text-gray-500 cursor-pointer" />
                                    <span class="absolute left-full top-1/2 transform -translate-y-1/2 ml-4 bg-gray-800 text-white text-xs rounded-md px-3 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300 whitespace-nowrap z-10 pointer-events-none">
                                        Enable this to apply sale price.
                                    </span>
                                </span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <label class="switch">
                                    <input disabled type="checkbox" v-model="editProduct.on_sale" true-value="yes" false-value="no" />
                                    <span class="slider round"></span>
                                </label>
                                <span class="text-xs text-gray-700">{{ editProduct.on_sale === 'yes' ? 'Yes' : 'No' }}</span>
                            </div>
                            <input disabled v-if="editProduct.on_sale === 'yes'" type="number" id="edit_on_sale_price" v-model="editProduct.on_sale_price" class="input-field mt-2 text-xs p-1" placeholder="On Sale Price (PHP)" />
                        </div>
                    </div>
                    <div class="col-span-3 flex justify-center mt-3 space-x-2">
                        <button @click="showViewProductModal = false" class="px-4 py-2 block text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 hover:scale-105 duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        </transition>

        <transition name="modal-fade" >
            <CategoriesModal v-if="showCategoriesModal" 
                    @close-categories-modal="showCategoriesModal = false" 
                    @category-added="fetchListedCategories" />
        </transition>
    </AuthenticatedLayout>
</template>


<style scoped>


.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.1s ease, transform 0.1s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.modal-fade-enter-to,
.modal-fade-leave-from {
  opacity: 1;
  transform: scale(1);
}


.toast-enter-active,
.toast-leave-active {
  transition: all 0.5s ease;
}
.toast-enter-from,
.toast-leave-to {
  transform: translateY(-100%);
  opacity: 0;
}

@keyframes popIn {
    0% {
      transform: scale(0.75);
      opacity: 0;
    }
    100% {
      transform: scale(1);
      opacity: 1;
    }
  }
  .pop-in {
    animation: popIn 0.1s ease-out;
  }

  @keyframes popOut {
    100% {
      transform: scale(1);
      opacity: 1;
    }
    0% {
      transform: scale(0.75);
      opacity: 0;
    }
  }
  .pop-out {
    animation: popIn 0.1s ease-in;
  }
  
.input-field {
    width: 100%;
    padding: 0.4rem 0.6rem;
    border: 1px solid #CBD5E0;
    border-bottom-right-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.input-field:focus {
    border-color: #3182CE;
    box-shadow: 0 0 0 1px #3182CE;
    outline: none;
}

.button-cancel {
    background-color: #DC2626;
    color: #FFFFFF;
    padding: 0.4rem 1rem;
    border-radius: 0.375rem;
    font-weight: 600;
    transition: background-color 0.2s ease-in-out;
}

.button-cancel:hover {
    background-color: #B91C1C;
}

.button-ok {
    background-color: #059669;
    color: #FFFFFF;
    padding: 0.4rem 1rem;
    border-radius: 0.375rem;
    font-weight: 600;
    transition: background-color 0.2s ease-in-out;
}

.button-ok:hover {
    background-color: #047857;
}



.switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 20px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 12px;
    width: 12px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #4CAF50;
}

input:checked + .slider:before {
    transform: translateX(20px);
}

input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0; /* Make the input invisible */
    cursor: pointer; 
    z-index: 10; /* Bring the input above other elements */
}

.image-upload {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
}



.image-upload {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
}
.image-upload svg,
.image-upload img {
    position: absolute;
    z-index: 1; /* Ensure the preview image is below the file input */
}

</style>