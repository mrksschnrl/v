// File: /v/packages/file-sort/js/utils-date.js
/**
 * Liefert für verschiedene Modi immer ein { startDate, endDate }-Objekt zurück.
 */
export function getDateRange(mode, inputs = {}) {
  const today = new Date();
  let startDate, endDate;

  switch (mode) {
    case "today":
      startDate = new Date(today);
      endDate = new Date(today);
      break;

    case "yesterday":
      startDate = new Date(today);
      startDate.setDate(startDate.getDate() - 1);
      endDate = new Date(startDate);
      break;

    case "last7":
      endDate = new Date(today);
      startDate = new Date(today);
      startDate.setDate(startDate.getDate() - 6);
      break;

    case "specific":
      if (!inputs.specificDay) {
        throw new Error(
          'Für "Bestimmter Tag" muss specificDay angegeben sein.'
        );
      }
      startDate = new Date(inputs.specificDay);
      endDate = new Date(inputs.specificDay);
      break;

    case "range":
      if (!inputs.rangeFrom || !inputs.rangeTo) {
        throw new Error(
          'Für "Von–Bis" müssen rangeFrom und rangeTo angegeben sein.'
        );
      }
      startDate = new Date(inputs.rangeFrom);
      endDate = new Date(inputs.rangeTo);
      break;

    default:
      throw new Error(`Unbekannter Modus: ${mode}`);
  }

  startDate.setHours(0, 0, 0, 0);
  endDate.setHours(23, 59, 59, 999);

  return { startDate, endDate };
}
