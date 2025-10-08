import { HeroSection } from "./components/HeroSection";
import { HowItWorksSection } from "./components/HowItWorksSection";
import { WhoItsForSection } from "./components/WhoItsForSection";
import { PurposeSection } from "./components/PurposeSection";
import { TherapistProfilesSection } from "./components/TherapistProfilesSection";
import { InterestSection } from "./components/InterestSection";
import { Footer } from "./components/Footer";

export default function App() {
  return (
    <div className="min-h-screen bg-[var(--therapair-alabaster)] text-[var(--therapair-charcoal)]">
      <HeroSection />
      <HowItWorksSection />
      <WhoItsForSection />
      <PurposeSection />
      <TherapistProfilesSection />
      <InterestSection />
      <Footer />
    </div>
  );
}