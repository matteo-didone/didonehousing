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
        "distanceToBase": "Distance to Base"
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
        "distanceToBase": "Distanza dalla Base"
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
