import moment from 'moment';
import 'moment/dist/locale/pt-br';
import {
  type SupportedLanguageConfig,
  type SupportedLanguages,
  supportedLanguages,
} from '../constants/supportedLanguages';

const DEFAULT_LANGUAGE = 'pt-BR';

export const formatCurrency = (
  value: number,
  langParam: SupportedLanguages = DEFAULT_LANGUAGE
): string => {
  const { lang, currency }: SupportedLanguageConfig =
    supportedLanguages[langParam];
  moment.locale(lang);
  return new Intl.NumberFormat(lang, {
    style: 'currency',
    currency: currency.id,
  }).format(value);
};

export const formatDate = (
  date: string | Date,
  langParam: SupportedLanguages = DEFAULT_LANGUAGE
): string => {
  const { lang }: SupportedLanguageConfig = supportedLanguages[langParam];
  moment.locale(lang);
  const dateParam = moment(date);
  if (!dateParam.isValid()) {
    return '';
  }
  return moment(dateParam).format('L');
};
