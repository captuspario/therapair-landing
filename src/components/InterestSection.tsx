import { Button } from "./ui/button";
import { Card } from "./ui/card";
import { Heart, Stethoscope, Building2, TrendingUp, HelpCircle } from "lucide-react";

export function InterestSection() {
  const interestTypes = [
    {
      icon: Heart,
      title: "I'm looking for therapy",
      description: "Get early access to our therapist matching platform",
      buttonText: "Join as a client",
      dataRole: "client-interest",
      color: "bg-[var(--therapair-rosewood)]",
    },
    {
      icon: Stethoscope,
      title: "I'm a therapist",
      description: "Join our network of inclusive mental health professionals",
      buttonText: "Apply to join",
      dataRole: "therapist-interest",
      color: "bg-[var(--therapair-calm-clay)]",
    },
    {
      icon: Building2,
      title: "I'm an organisation",
      description: "Partner with us to improve mental health outcomes",
      buttonText: "Learn about partnerships",
      dataRole: "organization-interest",
      color: "bg-[var(--therapair-rosewood)]",
    },
    {
      icon: TrendingUp,
      title: "I'm an investor",
      description: "Support the future of inclusive mental healthcare",
      buttonText: "Investment opportunities",
      dataRole: "investor-interest",
      color: "bg-[var(--therapair-calm-clay)]",
    },
    {
      icon: HelpCircle,
      title: "Just curious",
      description: "Stay updated on our progress and launch",
      buttonText: "Follow our journey",
      dataRole: "general-interest",
      color: "bg-[var(--therapair-rosewood)]",
    },
  ];

  return (
    <section className="py-16 px-4 sm:px-6 lg:px-8 bg-white/50">
      <div className="max-w-6xl mx-auto">
        <div className="text-center mb-12">
          <h2 className="mb-4 text-3xl sm:text-4xl">Want to be part of it?</h2>
          <p className="text-lg opacity-80 max-w-2xl mx-auto">
            Whether you're seeking therapy, providing it, or supporting our mission, there's a place for you in the Therapair community.
          </p>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {interestTypes.map((type, index) => (
            <Card key={index} className="p-6 border border-[var(--therapair-rosewood)]/20 bg-white/80 backdrop-blur-sm rounded-2xl hover:shadow-lg transition-all duration-300">
              <div className={`w-12 h-12 mb-4 rounded-xl ${type.color} flex items-center justify-center`}>
                <type.icon className="w-6 h-6 text-white" />
              </div>
              <h3 className="mb-3 text-lg">{type.title}</h3>
              <p className="mb-6 text-sm opacity-80 leading-relaxed">{type.description}</p>
              <Button 
                className={`w-full ${type.color} hover:opacity-90 text-white rounded-xl transition-all duration-200`}
                data-role={type.dataRole}
              >
                {type.buttonText}
              </Button>
            </Card>
          ))}
        </div>
        
        <div className="mt-12 p-6 bg-[var(--therapair-calm-clay)]/10 rounded-2xl border border-[var(--therapair-rosewood)]/20">
          <div className="text-center">
            <h3 className="mb-3 text-xl">Ready to get started?</h3>
            <p className="mb-4 opacity-80">
              All forms connect to our Mailchimp system for updates and early access notifications.
            </p>
            <p className="text-sm opacity-70">
              We respect your privacy and will never share your information. Unsubscribe anytime.
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}