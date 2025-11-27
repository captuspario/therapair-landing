import React from "react";

export interface TherapairLogoProps {
  size?: number;
}

// Core Therapair logo mark: interwoven symbol + wordmark
// Aligned with the Option A purple / Open Sans corporate identity.
export function TherapairLogo({ size = 40 }: TherapairLogoProps) {
  const color = "#9B74B7"; // therapair purple base

  return (
    <div className="inline-flex items-center gap-3" aria-label="Therapair logo">
      <svg
        width={size}
        height={size}
        viewBox="0 0 48 48"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M12 16 Q18 10, 24 16 T36 16"
          stroke={color}
          strokeWidth={3.5}
          strokeLinecap="round"
          fill="none"
        />
        <path
          d="M36 32 Q30 38, 24 32 T12 32"
          stroke={color}
          strokeWidth={3.5}
          strokeLinecap="round"
          fill="none"
        />
        <circle cx={24} cy={24} r={2.5} fill={color} />
        <circle cx={18} cy={16} r={1.8} fill={color} opacity={0.6} />
        <circle cx={30} cy={32} r={1.8} fill={color} opacity={0.6} />
      </svg>

      <span
        style={{
          fontFamily:
            "'Open Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
          fontWeight: 600,
          fontSize: `${size * 0.7}px`,
          color: "#1E293B",
          letterSpacing: "-0.02em",
        }}
      >
        Therapair
      </span>
    </div>
  );
}


