import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs) {
  return twMerge(clsx(inputs));
}

export function valueUpdater(updaterOrValue, ref) {
  ref.value =
    typeof updaterOrValue === "function"
      ? updaterOrValue(ref.value)
      : updaterOrValue;
}

export function getLastWordFromUrl(url) {
    // Remove any trailing slashes
    url = url.replace(/\/+$/, '');

    // Split by slash
    const parts = url.split('/');

    // Get the last part
    const lastSegment = parts[parts.length - 1];

    // Capitalize the first letter
    return lastSegment.charAt(0).toUpperCase() + lastSegment.slice(1);
}
