import React from "react";

export interface TherapairLogoProps {
  size?: number;
  showText?: boolean;
  variant?: 'full-color' | 'navy' | 'white';
  layout?: 'horizontal' | 'stacked';
  textSize?: 'large' | 'medium' | 'small';
}

// TheraPairIcon component from Logo System Exploration
function TheraPairIcon({ size = 64, variant = 'full-color' }: { size?: number; variant?: 'full-color' | 'navy' | 'white' }) {
  const colors = {
    'full-color': {
      navy: '#0F1E4B',
      midBlue: '#3D578A',
      lightBlue: '#95B1CD',
    },
    'navy': {
      navy: '#0F1E4B',
      midBlue: '#0F1E4B',
      lightBlue: '#0F1E4B',
    },
    'white': {
      navy: '#FFFFFF',
      midBlue: '#FFFFFF',
      lightBlue: '#FFFFFF',
    },
  };

  const currentColors = colors[variant];
  const aspectRatio = 409 / 391; // width / height from viewBox

  return (
    <div style={{ width: size, height: size / aspectRatio }}>
      <svg 
        className="block size-full" 
        fill="none" 
        preserveAspectRatio="xMidYMid meet" 
        viewBox="0 0 409 391"
      >
        <g>
          {/* Center dark navy pill */}
          <rect 
            fill={currentColors.navy}
            height="184.001" 
            rx="92.0005" 
            transform="rotate(90 294.82 7.31618)" 
            width="383.684" 
            x="294.82" 
            y="7.31618" 
          />
          
          {/* Mid blue heart shape */}
          <path 
            d="M204.02 7.31616C148.978 7.31616 110.819 51.8734 110.819 95.6576C110.819 95.6576 110.819 169.816 110.819 197.59C178.251 197.59 204.02 273.909 204.02 273.909C204.02 273.909 225.297 197.59 294.82 197.59C294.82 172.43 295.279 95.6576 295.279 95.6576C295.279 54.8141 259.061 7.31616 204.02 7.31616Z"
            fill={currentColors.midBlue}
          />
          
          {/* Light blue left ear */}
          <path 
            d="M202.344 0C147.556 0.173406 103.194 44.6403 103.194 99.4698V197.954H98.9767C44.3133 197.954 0 153.64 0 98.9767C0.000150894 44.3134 44.3134 0.000144158 98.9767 0H202.344Z"
            fill={currentColors.lightBlue}
          />
          
          {/* Light blue right ear */}
          <path 
            d="M309.798 0C364.461 0.000154817 408.775 44.3134 408.775 98.9767C408.775 153.64 364.461 197.954 309.798 197.954H302.138V99.4698C302.138 44.6403 257.777 0.173402 202.988 0H309.798Z"
            fill={currentColors.lightBlue}
          />
        </g>
      </svg>
    </div>
  );
}

// Core Therapair logo: TheraPairIcon + wordmark + tagline
// Using new design system: Dark Navy/Mid Blue/Light Blue colors, Inter typography
export function TherapairLogo({ 
  size = 64, 
  showText = true, 
  variant = 'full-color',
  layout = 'horizontal',
  textSize = 'medium'
}: TherapairLogoProps) {
  const wordmarkSizes = {
    large: { fontSize: '48px', taglineSize: '14px' },
    medium: { fontSize: '40px', taglineSize: '11px' },
    small: { fontSize: '24px', taglineSize: '7px' },
  };

  const sizes = wordmarkSizes[textSize];
  const iconSize = size;

  const wordmark = showText && (
    <div className="space-y-0.5">
      <div
        className="tracking-tight"
        style={{
          fontFamily: 'Inter, sans-serif',
          fontWeight: 700,
          fontSize: sizes.fontSize,
          color: variant === 'white' ? '#FFFFFF' : '#000000',
          lineHeight: 1.2,
        }}
      >
        TheraPair
      </div>
      <div
        className="tracking-[0.15em]"
        style={{
          fontFamily: 'Inter, sans-serif',
          fontWeight: 400,
          fontSize: sizes.taglineSize,
          color: variant === 'white' ? '#FFFFFF' : '#000000',
          letterSpacing: '0.15em',
        }}
      >
        SMART THERAPY MATCHING
      </div>
    </div>
  );

  if (layout === 'stacked') {
    return (
      <div className="flex flex-col items-center gap-4">
        <TheraPairIcon size={iconSize} variant={variant} />
        {wordmark}
      </div>
    );
  }

  return (
    <div className="flex items-center gap-4" aria-label="Therapair logo">
      <TheraPairIcon size={iconSize} variant={variant} />
      {wordmark}
    </div>
  );
}
