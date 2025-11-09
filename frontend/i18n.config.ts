export default defineI18nConfig(() => ({
  legacy: false,
  locale: 'en',
  fallbackLocale: 'en',
  messages: {
    en: {
      "common": {
        "welcome": "Welcome to Aviano Housing",
        "loading": "Loading...",
        "save": "Save",
        "cancel": "Cancel",
        "delete": "Delete",
        "edit": "Edit",
        "view": "View",
        "search": "Search",
        "filter": "Filter",
        "back": "Back",
        "next": "Next",
        "previous": "Previous",
        "submit": "Submit",
        "close": "Close",
        "confirm": "Confirm",
        "yes": "Yes",
        "no": "No",
        "select": "Select",
        "upload": "Upload",
        "download": "Download",
        "print": "Print",
        "export": "Export",
        "import": "Import",
        "refresh": "Refresh",
        "reset": "Reset",
        "apply": "Apply",
        "clear": "Clear",
        "noData": "No data available",
        "error": "An error occurred",
        "success": "Operation successful"
      },
      "auth": {
        "login": "Log In",
        "logout": "Log Out",
        "register": "Sign Up",
        "loginTitle": "Sign in to your account",
        "loginSubtitle": "Enter your credentials to access Aviano Housing Platform",
        "registerTitle": "Create your account",
        "registerSubtitle": "Join the Aviano Housing community",
        "forgotPassword": "Forgot Password?",
        "resetPassword": "Reset Password",
        "email": "Email address",
        "emailPlaceholder": "Enter your email",
        "password": "Password",
        "passwordPlaceholder": "Enter your password",
        "confirmPassword": "Confirm Password",
        "rememberMe": "Remember me",
        "signingIn": "Signing in...",
        "noAccount": "Don't have an account?",
        "hasAccount": "Already have an account?",
        "createAccount": "Create account",
        "loginSuccess": "Successfully logged in",
        "logoutSuccess": "Successfully logged out",
        "loginError": "Invalid credentials. Please try again.",
        "loginRequired": "Please sign in to continue"
      },
      "navigation": {
        "home": "Home",
        "dashboard": "Dashboard",
        "properties": "Properties",
        "leases": "Leases",
        "payments": "Payments",
        "maintenance": "Maintenance",
        "documents": "Documents",
        "messages": "Messages",
        "profile": "Profile",
        "settings": "Settings"
      },
      "property": {
        "title": "Properties",
        "search": "Search properties",
        "addNew": "Add Property",
        "viewDetails": "View Details",
        "address": "Address",
        "rent": "Monthly Rent",
        "bedrooms": "Bedrooms",
        "bathrooms": "Bathrooms",
        "squareMeters": "Square Meters",
        "availableFrom": "Available From",
        "status": "Status",
        "distanceToBase": "Distance to Base",
        "photos": {
          "title": "Property Photos",
          "subtitle": "Upload photos of your property to attract more tenants",
          "dragDrop": "Click or drag photos here to upload",
          "uploadHint": "Supported formats: JPEG, PNG, WebP (max 5MB per photo)",
          "description": "Photo Description",
          "descriptionPlaceholder": "Describe this photo (e.g., Living room, Kitchen, etc.)",
          "uploaded": "Uploaded successfully"
        },
        "documents": {
          "title": "Property Documents",
          "subtitle": "Upload required documents for your property listing",
          "dragDrop": "Click or drag documents here to upload",
          "uploadHint": "Supported formats: PDF, DOC, DOCX, JPEG, PNG (max 10MB per file)",
          "type": "Document Type",
          "description": "Document Description",
          "descriptionPlaceholder": "Add notes about this document (optional)",
          "uploaded": "Uploaded successfully",
          "types": {
            "cadastralSurvey": "Cadastral Survey (Visura Catastale)",
            "energyCertificate": "Energy Performance Certificate (APE)",
            "proofOfOwnership": "Proof of Ownership",
            "other": "Other"
          }
        },
        "create": {
          "title": "Create New Property",
          "subtitle": "Fill in the details about your property",
          "success": "Property created successfully!",
          "successDescription": "Your property has been added to the platform."
        },
        "types": {
          "apartment": "Apartment",
          "house": "House",
          "villa": "Villa",
          "studio": "Studio"
        }
      },
      "validation": {
        "required": "This field is required",
        "email": "Please enter a valid email",
        "min": "Minimum {min} characters required",
        "max": "Maximum {max} characters allowed",
        "numeric": "Must be a number",
        "passwordMatch": "Passwords do not match"
      }
    },
    it: {
      "common": {
        "welcome": "Benvenuto ad Aviano Housing",
        "loading": "Caricamento...",
        "save": "Salva",
        "cancel": "Annulla",
        "delete": "Elimina",
        "edit": "Modifica",
        "view": "Visualizza",
        "search": "Cerca",
        "filter": "Filtra",
        "back": "Indietro",
        "next": "Avanti",
        "previous": "Precedente",
        "submit": "Invia",
        "close": "Chiudi",
        "confirm": "Conferma",
        "yes": "Sì",
        "no": "No",
        "select": "Seleziona",
        "upload": "Carica",
        "download": "Scarica",
        "print": "Stampa",
        "export": "Esporta",
        "import": "Importa",
        "refresh": "Aggiorna",
        "reset": "Reimposta",
        "apply": "Applica",
        "clear": "Cancella",
        "noData": "Nessun dato disponibile",
        "error": "Si è verificato un errore",
        "success": "Operazione completata con successo"
      },
      "auth": {
        "login": "Accedi",
        "logout": "Esci",
        "register": "Registrati",
        "loginTitle": "Accedi al tuo account",
        "loginSubtitle": "Inserisci le tue credenziali per accedere alla Piattaforma Aviano Housing",
        "registerTitle": "Crea il tuo account",
        "registerSubtitle": "Unisciti alla community Aviano Housing",
        "forgotPassword": "Password dimenticata?",
        "resetPassword": "Reimposta Password",
        "email": "Indirizzo email",
        "emailPlaceholder": "Inserisci la tua email",
        "password": "Password",
        "passwordPlaceholder": "Inserisci la tua password",
        "confirmPassword": "Conferma Password",
        "rememberMe": "Ricordami",
        "signingIn": "Accesso in corso...",
        "noAccount": "Non hai un account?",
        "hasAccount": "Hai già un account?",
        "createAccount": "Crea account",
        "loginSuccess": "Accesso effettuato con successo",
        "logoutSuccess": "Disconnessione effettuata con successo",
        "loginError": "Credenziali non valide. Riprova.",
        "loginRequired": "Effettua l'accesso per continuare"
      },
      "navigation": {
        "home": "Home",
        "dashboard": "Pannello",
        "properties": "Proprietà",
        "leases": "Contratti",
        "payments": "Pagamenti",
        "maintenance": "Manutenzione",
        "documents": "Documenti",
        "messages": "Messaggi",
        "profile": "Profilo",
        "settings": "Impostazioni"
      },
      "property": {
        "title": "Proprietà",
        "search": "Cerca proprietà",
        "addNew": "Aggiungi Proprietà",
        "viewDetails": "Visualizza Dettagli",
        "address": "Indirizzo",
        "rent": "Affitto Mensile",
        "bedrooms": "Camere da Letto",
        "bathrooms": "Bagni",
        "squareMeters": "Metri Quadrati",
        "availableFrom": "Disponibile Dal",
        "status": "Stato",
        "distanceToBase": "Distanza dalla Base",
        "photos": {
          "title": "Foto della Proprietà",
          "subtitle": "Carica foto della tua proprietà per attirare più inquilini",
          "dragDrop": "Clicca o trascina le foto qui per caricare",
          "uploadHint": "Formati supportati: JPEG, PNG, WebP (max 5MB per foto)",
          "description": "Descrizione Foto",
          "descriptionPlaceholder": "Descrivi questa foto (es. Soggiorno, Cucina, ecc.)",
          "uploaded": "Caricato con successo"
        },
        "documents": {
          "title": "Documenti della Proprietà",
          "subtitle": "Carica i documenti richiesti per il tuo annuncio",
          "dragDrop": "Clicca o trascina i documenti qui per caricare",
          "uploadHint": "Formati supportati: PDF, DOC, DOCX, JPEG, PNG (max 10MB per file)",
          "type": "Tipo di Documento",
          "description": "Descrizione Documento",
          "descriptionPlaceholder": "Aggiungi note su questo documento (facoltativo)",
          "uploaded": "Caricato con successo",
          "types": {
            "cadastralSurvey": "Visura Catastale",
            "energyCertificate": "Attestato di Prestazione Energetica (APE)",
            "proofOfOwnership": "Prova di Proprietà",
            "other": "Altro"
          }
        },
        "create": {
          "title": "Crea Nuova Proprietà",
          "subtitle": "Compila i dettagli della tua proprietà",
          "success": "Proprietà creata con successo!",
          "successDescription": "La tua proprietà è stata aggiunta alla piattaforma."
        },
        "types": {
          "apartment": "Appartamento",
          "house": "Casa",
          "villa": "Villa",
          "studio": "Monolocale"
        }
      },
      "validation": {
        "required": "Questo campo è obbligatorio",
        "email": "Inserisci un'email valida",
        "min": "Minimo {min} caratteri richiesti",
        "max": "Massimo {max} caratteri consentiti",
        "numeric": "Deve essere un numero",
        "passwordMatch": "Le password non corrispondono"
      }
    }
  },
  datetimeFormats: {
    en: {
      short: {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      },
      long: {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        weekday: 'long'
      },
      time: {
        hour: '2-digit',
        minute: '2-digit'
      },
      datetime: {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }
    },
    it: {
      short: {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      },
      long: {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        weekday: 'long'
      },
      time: {
        hour: '2-digit',
        minute: '2-digit'
      },
      datetime: {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }
    }
  },
  numberFormats: {
    en: {
      currency: {
        style: 'currency',
        currency: 'EUR',
        notation: 'standard'
      },
      decimal: {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      },
      percent: {
        style: 'percent',
        useGrouping: false
      }
    },
    it: {
      currency: {
        style: 'currency',
        currency: 'EUR',
        notation: 'standard'
      },
      decimal: {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      },
      percent: {
        style: 'percent',
        useGrouping: false
      }
    }
  }
}))
