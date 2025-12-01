import { Instagram, Linkedin, Mail } from "lucide-react";
import { TherapairLogo } from "./TherapairLogo";

export function Footer() {
  return (
    <footer className="py-12 px-4 sm:px-6 lg:px-8 bg-[#0F1E4B] text-white">
      <div className="max-w-6xl mx-auto">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
          {/* Brand */}
          <div>
            <div className="mb-3">
              <TherapairLogo size={28} variant="white" />
            </div>
            <p className="text-sm opacity-80">
              Therapist matching for inclusive mental healthcare
            </p>
          </div>
          
          {/* Contact */}
          <div className="text-center">
            <div className="flex items-center justify-center gap-2 mb-3">
              <Mail className="w-4 h-4" />
              <a 
                href="mailto:hello@therapair.com" 
                className="hover:text-[#95B1CD] transition-colors"
              >
                hello@therapair.com
              </a>
            </div>
            <div className="flex items-center justify-center gap-4">
              <a 
                href="#" 
                className="hover:text-[#95B1CD] transition-colors"
                aria-label="Instagram"
              >
                <Instagram className="w-5 h-5" />
              </a>
              <a 
                href="#" 
                className="hover:text-[#95B1CD] transition-colors"
                aria-label="LinkedIn"
              >
                <Linkedin className="w-5 h-5" />
              </a>
            </div>
          </div>
          
          {/* Quote and Links */}
          <div className="text-center md:text-right">
            <p className="italic mb-4 text-[#95B1CD]">
              "Real change starts with the right connection."
            </p>
            <div className="flex flex-wrap justify-center md:justify-end gap-4 text-sm">
              <a href="#" className="hover:text-[#95B1CD] transition-colors">
                Privacy Policy
              </a>
              <a href="#" className="hover:text-[#95B1CD] transition-colors">
                Terms of Service
              </a>
            </div>
          </div>
        </div>
        
        <div className="border-t border-[#95B1CD]/30 mt-8 pt-8 text-center">
          <p className="text-sm opacity-70">
            © 2025 Therapair. Building a more inclusive future for mental healthcare.
          </p>
        </div>
      </div>
    </footer>
  );
}