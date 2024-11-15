export type SupportedLanguages = 'pt-BR' | 'en-US';

export type SupportedLanguageConfig = {
  lang: string;
  currency: {
    id: string;
  };
};

export const supportedLanguages = {
  'pt-BR': {
    lang: 'pt-BR',
    currency: {
      id: 'BRL',
    },
  },
  'en-US': {
    lang: 'en-US',
    currency: {
      id: 'USD',
    },
  },
};
