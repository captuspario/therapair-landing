import React from "react";

export interface TherapairLogoProps {
  size?: number;
  showText?: boolean;
  variant?: 'full-color' | 'navy' | 'white';
  layout?: 'horizontal' | 'stacked';
  textSize?: 'large' | 'medium' | 'small';
}

// Core Therapair logo using the final PNG file
export function TherapairLogo({ 
  size = 64, 
  showText = true, 
  variant = 'full-color',
  layout = 'horizontal',
  textSize = 'medium'
}: TherapairLogoProps) {
  // Calculate image dimensions based on size prop
  // The logo PNG maintains its aspect ratio
  const imageStyle: React.CSSProperties = {
    width: size === 64 ? '300px' : size === 28 ? '150px' : `${size * 4.7}px`,
    height: 'auto',
    maxWidth: '100%',
  };

  if (layout === 'stacked') {
    return (
      <div className="flex flex-col items-center gap-4">
        <img 
          src="/images/therapair-logo-final.png" 
          alt="Therapair Logo" 
          style={imageStyle}
        />
      </div>
    );
  }

  return (
    <div className="flex items-center gap-4" aria-label="Therapair logo">
      <img 
        src="/images/therapair-logo-final.png" 
        alt="Therapair Logo" 
        style={imageStyle}
      />
    </div>
  );
}
