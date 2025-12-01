import { createRoot } from "react-dom/client";
import AnalyticsApp from "./App";
import "../index.css";

const mountNode = document.getElementById("root");

if (!mountNode) {
  throw new Error("Analytics root element not found");
}

createRoot(mountNode).render(<AnalyticsApp />);

