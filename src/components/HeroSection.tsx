import { Button } from "./ui/button";
import { Input } from "./ui/input";
import { ImageWithFallback } from "./figma/ImageWithFallback";
import { TherapairLogo } from "./TherapairLogo";

export function HeroSection() {
  return (
    <section className="relative min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
      {/* Background image with overlay */}
      <div className="absolute inset-0 z-0">
        <ImageWithFallback
          src="https://images.unsplash.com/photo-1620924701256-1c6f1103ebdf?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxkaXZlcnNlJTIwdGhlcmFweSUyMHNlc3Npb24lMjBjYWxtfGVufDF8fHx8MTc1OTkwMzY5NHww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
          alt="Diverse therapy session"
          className="w-full h-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-br from-[var(--therapair-alabaster)]/95 via-[var(--therapair-alabaster)]/90 to-[var(--therapair-calm-clay)]/20"></div>
      </div>
      
      {/* Hero content */}
      <div className="relative z-10 max-w-4xl mx-auto text-center space-y-6">
        <div className="flex justify-center">
          <TherapairLogo size={32} />
        </div>

        <div className="inline-block px-4 py-2 mb-6 rounded-full bg-[var(--therapair-calm-clay)]/20 border border-[var(--therapair-rosewood)]/30">
          <p className="text-sm">AI concierge for mental health</p>
        </div>
        
        <h1 className="mb-6 text-4xl sm:text-5xl lg:text-6xl">
          Therapy that fits. Finally.
        </h1>
        
        <p className="mb-8 text-lg sm:text-xl max-w-2xl mx-auto opacity-90">
          Answer a few simple questions, and we'll help you find a therapist who really gets you.
        </p>
        
        {/* Email signup */}
        <div className="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
          <Input
            type="email"
            placeholder="Enter your email"
            className="flex-1 bg-white/90 border-[var(--therapair-rosewood)]/30 focus:border-[var(--therapair-rosewood)] rounded-xl"
          />
          <Button 
            className="bg-[var(--therapair-rosewood)] hover:bg-[var(--therapair-terracotta)] text-white rounded-xl px-8 transition-colors duration-200"
            data-role="early-access-signup"
          >
            Join Early Access
          </Button>
        </div>
        
        <p className="mt-4 text-sm opacity-70">
          No spam, just updates on our launch.
        </p>
      </div>
    </section>
  );
}