import { useToast as primeVueUseToast } from 'primevue/usetoast';

export const useToast = () => {
  const internalToast = primeVueUseToast();

  return {
    showSuccess: (title: string, message: string = '', life: number = 3000) => {
      internalToast.add({
        severity: 'success',
        summary: title,
        detail: message,
        life,
      });
    },
    showInfo: (title: string, message: string = '', life: number = 3000) => {
      internalToast.add({
        severity: 'info',
        summary: title,
        detail: message,
        life,
      });
    },
    showWarn: (title: string, message: string = '', life: number = 3000) => {
      internalToast.add({
        severity: 'warn',
        summary: title,
        detail: message,
        life,
      });
    },
    showError: (title: string, message: string = '', life: number = 5000) => {
      internalToast.add({
        severity: 'error',
        summary: title,
        detail: message,
        life,
      });
    },
  };
};
